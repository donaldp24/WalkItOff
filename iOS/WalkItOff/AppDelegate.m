//
//  AppDelegate.m
//  WalkItOff
//
//  Created by Donald Pae on 6/7/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "AppDelegate.h"
#import "CommonMethods.h"
#import "AppContext.h"
#import "NSDate+walkitoff.h"
#import "Model.h"
#import "Formulas+walkitoff.h"
#import "UserContext.h"
#import "AppSettings.h"
#import <Social/Social.h>

@implementation AppDelegate

- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions
{
    // Override point for customization after application launch.
    
    // Change the tab bar background
    /*
    UIImage *tabBarBackground = [UIImage imageNamed:@"HomeActTabbar.png"];
    [[UITabBar appearance] setBackgroundImage:tabBarBackground];
    [[UITabBar appearance] setTintColor:[UIColor whiteColor]];
     */

    // Get top-level view controller reference programmatically
    //self.tabBarController = (UITabBarController *) self.window.rootViewController;
    // Set delegate of the tabBarController to handle the UITabBarControllerDelegate calls
    //self.tabBarController.delegate = self;
    
    // set pedometer
    Pedometer *defaultPedometer = [Pedometer defaultPedometer];
    self.pedometer = defaultPedometer;
    self.pedometer.delegate = self;
    
    _accountStore = [[ACAccountStore alloc] init];
    
    // have to start when open main view controller [loginviewcontroller gotoMain] functions,
    // so this is commented
//    AppContext *context = [AppContext sharedContext];
//    if (context.pedometerStarted)
//    {
//        [self.pedometer start];
//    }
    
  
    return YES;
}
							
- (void)applicationWillResignActive:(UIApplication *)application
{
    // Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
    // Use this method to pause ongoing tasks, disable timers, and throttle down OpenGL ES frame rates. Games should use this method to pause the game.
}

- (void)applicationDidEnterBackground:(UIApplication *)application
{
    // Use this method to release shared resources, save user data, invalidate timers, and store enough application state information to restore your application to its current state in case it is terminated later. 
    // If your application supports background execution, this method is called instead of applicationWillTerminate: when the user quits.
}

- (void)applicationWillEnterForeground:(UIApplication *)application
{
    // Called as part of the transition from the background to the inactive state; here you can undo many of the changes made on entering the background.
}

- (void)applicationDidBecomeActive:(UIApplication *)application
{
    // Restart any tasks that were paused (or not yet started) while the application was inactive. If the application was previously in the background, optionally refresh the user interface.
    
    
    // Handle the user leaving the app while the Facebook login dialog is being shown
    // For example: when the user presses the iOS "home" button while the login dialog is active
    [FBAppCall handleDidBecomeActive];
    
    
    UserContext *userContext = [UserContext sharedContext];

    if (userContext.isLoggedIn == YES)
    {
        AppContext *context = [AppContext sharedContext];
        
        [self checkStepsAndSave:[NSDate date]];
        
        // sent it to delegate
        if (self.pedometerViewerDelegate)
        {
            [self.pedometerViewerDelegate updateNumberOfSteps:context.numberOfTodaySteps];
        }
    }
    
}

- (void)applicationWillTerminate:(UIApplication *)application
{
    // Called when the application is about to terminate. Save data if appropriate. See also applicationDidEnterBackground:.
}

// During the Facebook login flow, your app passes control to the Facebook iOS app or Facebook in a mobile browser.
// After authentication, your app will be called back with the session information.
- (BOOL)application:(UIApplication *)application
            openURL:(NSURL *)url
  sourceApplication:(NSString *)sourceApplication
         annotation:(id)annotation
{
    return [FBAppCall handleOpenURL:url sourceApplication:sourceApplication];
}


- (void)tabBarController:(UITabBarController *)tabBarController didSelectViewController:(UIViewController *)viewController
{
    /*
    if (tabBarController.selectedIndex == 0) {
        [tabBarController.tabBar setBackgroundImage:[UIImage imageNamed:@"HomeActTabbar.png"]];
        [tabBarController.tabBar setTintColor:[UIColor redColor]];
    } else if (tabBarController.selectedIndex == 1) {
        [tabBarController.tabBar setBackgroundImage:[UIImage imageNamed:@"LessonProgressActTabbar.png"]];
    } else if (tabBarController.selectedIndex == 2) {
        [tabBarController.tabBar setBackgroundImage:[UIImage imageNamed:@"TrickActTabbar.png"]];
    } else if (tabBarController.selectedIndex == 3) {
        [tabBarController.tabBar setBackgroundImage:[UIImage imageNamed:@"MapActTabbar.png"]];
    }
    */
}

#pragma mark - Pedometer delegate
- (void)updateStepCounter:(NSInteger)numberOfSteps timestamp:(NSDate *)timestamp
{

    if ([UserContext sharedContext].isLoggedIn == NO)
    {
        NSLog(@"%@", @"error - Pedometer started without logging in");
        return;
    }
    
    AppContext *context = [AppContext sharedContext];
    

    
    // process steps taken today
    [self checkStepsAndSave:timestamp];
    
    // add step count
    context.numberOfTodaySteps++;
    context.lastTimestamp = timestamp;
    
    // add total steps taken
    context.stepsTaken++;
    
    [context save];
    
    // sent it to delegate
    if (self.pedometerViewerDelegate)
    {
        [self.pedometerViewerDelegate updateNumberOfSteps:context.stepsTaken];
    }
    
    
    CGFloat userCaloriesBurnedPerStep = [Formulas userCaloriesBurnedPerStep:[Formulas userCaloriesBurnedPerMile:[Formulas weightInLbsWithKg:[User currentUser].weight]] strideLengthInMiles:[Formulas userStrideLengthInMiles:[User currentUser].height]];
    CGFloat caloriesBurned = context.stepsTaken * userCaloriesBurnedPerStep;
    
    // post when 500 calories burned
    if (caloriesBurned > context.nextPostCalories)
    {
        context.nextPostCalories = caloriesBurned + POST_CALORIES_MILESTONE;
        
        NSString *msgToPost = [NSString stringWithFormat:@"You have consumed %d calories", (int)caloriesBurned];
        if ([AppSettings sharedSettings].isPostPer500)
        {
            // post facebook message
            NSDictionary *params = [NSDictionary dictionaryWithObjectsAndKeys:
                                    msgToPost, @"message",
                                    nil
                                    ];
            /* make the API call */
            [FBRequestConnection startWithGraphPath:@"/me/feed"
                                         parameters:params
                                         HTTPMethod:@"POST"
                                  completionHandler:^(
                                                      FBRequestConnection *connection,
                                                      id result,
                                                      NSError *error
                                                      ) {
                                      /* handle the result */
                                  }];
        }
        
        if ([AppSettings sharedSettings].isTweetPer500)
        {
            [self postMessageToTwitter:msgToPost withUser:[AppSettings sharedSettings].twitterUser withPwd:[AppSettings sharedSettings].twitterPwd];
        }
    }
    
    // check all calories consumed
    
    // total calories
    CGFloat totalCalories = 0;
    for (Food *food in [User currentUser].currentFoods) {
        totalCalories += food.calories;
    }
    
    // check all calories consumed
    if (totalCalories > 0)
    {
        
        if (caloriesBurned >= totalCalories)
        {
            NSString *msgToPost = @"Congratulations! You have consumed all calories";
            if ([AppSettings sharedSettings].isPostWhenAllCalories)
            {
                // post facebook message
                NSDictionary *params = [NSDictionary dictionaryWithObjectsAndKeys:
                                        msgToPost, @"message",
                                        nil
                                        ];
                /* make the API call */
                [FBRequestConnection startWithGraphPath:@"/me/feed"
                                             parameters:params
                                             HTTPMethod:@"POST"
                                      completionHandler:^(
                                                          FBRequestConnection *connection,
                                                          id result,
                                                          NSError *error
                                                          ) {
                                          /* handle the result */
                                      }];
            }
            
            if ([AppSettings sharedSettings].isTweetWhenAllCalories)
            {
                [self postMessageToTwitter:msgToPost withUser:[AppSettings sharedSettings].twitterUser withPwd:[AppSettings sharedSettings].twitterPwd];
            }
            
            // mark current foods consumed
#ifdef _USE_REMOTE
            [Food consumedFoods:[User currentUser].uid arrayData:[User currentUser].currentFoods success:^()
             {
                 int stepsTaken = (int)context.stepsTaken;
                 context.resetDate = [NSDate date];
                 context.stepsTaken = 0;
                 
                 [context save];
                 
                 [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
                     [[User currentUser].currentFoods removeAllObjects];
                     if (self.pedometerViewerDelegate)
                         [self.pedometerViewerDelegate consumedCurrentFoods:stepsTaken withDate:[NSDate date]];
                 }];
                 
             } failure:^(NSString *msg) {
                 //
             }];
#else
            [Food consumedFoodsWithLocal:[User currentUser].uid arrayData:[User currentUser].currentFoods success:^() {
                
                int stepsTaken = (int)context.stepsTaken;
                context.resetDate = [NSDate date];
                context.stepsTaken = 0;
                
                [context save];
                
                [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
                    [[User currentUser].currentFoods removeAllObjects];
                    if (self.pedometerViewerDelegate)
                        [self.pedometerViewerDelegate consumedCurrentFoods:stepsTaken withDate:[NSDate date]];
                }];
                
            } failure:^(NSString *msg) {
                //
            }];
#endif
        }
    }
}

- (void)started
{
    AppContext *context = [AppContext sharedContext];
    context.pedometerStarted = YES;
    [context save];
}

- (void)stopped
{
    AppContext *context = [AppContext sharedContext];
    context.pedometerStarted = NO;
    [context save];
}

- (void)checkStepsAndSave:(NSDate *)timestamp
{
    AppContext *context = [AppContext sharedContext];
    if ([CommonMethods compareOnlyDate:timestamp date2:context.lastTimestamp] != NSOrderedSame)
    {
        // calc params and save
        // db->save(strDate, context.numberOfTodaySteps);
        Consumed *consumed = [[Consumed alloc] init];
        consumed.date = context.lastTimestamp;
        consumed.stepsTaken = (int)context.numberOfTodaySteps;
        
        consumed.caloriesConsumed = [Formulas userCaloriesBurnedPerStep:[Formulas userCaloriesBurnedPerMile:[Formulas weightInLbsWithKg:[User currentUser].weight]] strideLengthInMiles:[Formulas userStrideLengthInMiles:[User currentUser].height]] * context.numberOfTodaySteps;
        
        consumed.milesWalked = context.numberOfTodaySteps * [Formulas userStrideLengthInMiles:[User currentUser].height];
        
#ifdef _USE_REMOTE
        [Consumed addConsumed:[User currentUser].uid withConsumed:consumed success:^(){
            //
        } failure:^(NSString *msg) {
            // add consumed to local database and sync when online
            // local_db->save();
        }];
#else
        [Consumed addConsumedWithLocal:[User currentUser].uid withConsumed:consumed success:^(){
            //
        } failure:^(NSString *msg) {
            // add consumed to local database and sync when online
            // local_db->save();
        }];
#endif
        
        context.lastTimestamp = timestamp;
        context.numberOfTodaySteps = 0;
        
        [context save];
    }
}

- (void)postMessageToTwitter:(NSString *)msg withUser:(NSString *)user withPwd:(NSString *)pwd
{
    SLRequestHandler requestHandler =
    ^(NSData *responseData, NSHTTPURLResponse *urlResponse, NSError *error) {
        if (responseData) {
            NSInteger statusCode = urlResponse.statusCode;
            if (statusCode >= 200 && statusCode < 300) {
                NSDictionary *postResponseData =
                [NSJSONSerialization JSONObjectWithData:responseData
                                                options:NSJSONReadingMutableContainers
                                                  error:NULL];
                NSLog(@"[SUCCESS!] Created Tweet with ID: %@", postResponseData[@"id_str"]);
            }
            else {
                NSLog(@"[ERROR] Server responded: status code %d %@", (int)statusCode,
                      [NSHTTPURLResponse localizedStringForStatusCode:statusCode]);
            }
        }
        else {
            NSLog(@"[ERROR] An error occurred while posting: %@", [error localizedDescription]);
        }
    };
    
    ACAccountType *twitterType =
    [self.accountStore accountTypeWithAccountTypeIdentifier:ACAccountTypeIdentifierTwitter];
    
    ACAccountStoreRequestAccessCompletionHandler accountStoreHandler =
    ^(BOOL granted, NSError *error) {
        if (granted) {
            NSArray *accounts = [self.accountStore accountsWithAccountType:twitterType];
            
            ACAccount *account = nil;
            for (ACAccount *item in accounts) {
                if ([item.identifier isEqualToString:user])
                {
                    account = item;
                }
            }
            
            if (account)
            {
                NSURL *url = [NSURL URLWithString:@"https://api.twitter.com/1/direct_messages/new.forma"];
                NSDictionary *params = @{@"user_id" : [AppSettings sharedSettings].twitterUser,
                                         @"text" : msg};
                SLRequest *request = [SLRequest requestForServiceType:SLServiceTypeTwitter
                                                        requestMethod:SLRequestMethodPOST
                                                                  URL:url
                                                           parameters:params];
                
                [request setAccount:account];
                [request performRequestWithHandler:requestHandler];
            }
        }
        else {
            NSLog(@"[ERROR] An error occurred while asking for user authorization: %@",
                  [error localizedDescription]);
        }
    };
    
    [self.accountStore requestAccessToAccountsWithType:twitterType
                                               options:NULL
                                            completion:accountStoreHandler];
}

@end
