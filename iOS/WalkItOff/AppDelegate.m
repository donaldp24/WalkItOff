//
//  AppDelegate.m
//  WalkItOff
//
//  Created by Donald Pae on 6/7/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "AppDelegate.h"

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
}

- (void)applicationWillTerminate:(UIApplication *)application
{
    // Called when the application is about to terminate. Save data if appropriate. See also applicationDidEnterBackground:.
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

@end