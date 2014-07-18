//
//  FacebookLoginSettingsViewController.m
//  WalkItOff
//
//  Created by Donald Pae on 7/13/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "FacebookLoginSettingsViewController.h"
#import "UserContext.h"
#import "SVProgressHUD+walkitoff.h"
#import "UIManager.h"

@interface FacebookLoginSettingsViewController () {
    UIBarButtonItem *_backButton;
    UIResponder *currentResponder;
}

@property (nonatomic, strong) IBOutlet UIButton *btnLogin;

@end

@implementation FacebookLoginSettingsViewController

- (id)initWithStyle:(UITableViewStyle)style
{
    self = [super initWithStyle:style];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    // Uncomment the following line to preserve selection between presentations.
    // self.clearsSelectionOnViewWillAppear = NO;
    
    // Uncomment the following line to display an Edit button in the navigation bar for this view controller.
    // self.navigationItem.rightBarButtonItem = self.editButtonItem;
    
    //Hide empty separators
    UIView *v = [[UIView alloc] initWithFrame:CGRectZero];
    v.backgroundColor = [UIColor clearColor];
    self.tableView.tableFooterView = v;
    
    self.tableView.backgroundColor = [UIManager appBackgroundColor];
    
    // back button
    
    _backButton = [[UIBarButtonItem alloc] initWithImage:[UIImage imageNamed:@"backicon"] style:UIBarButtonItemStylePlain target:self action:@selector(onBack:)];
    self.navigationItem.leftBarButtonItem = _backButton;
    
    // check facebook auth token
    if (FBSession.activeSession.state == FBSessionStateOpen ||
        FBSession.activeSession.state == FBSessionStateOpenTokenExtended)
    {
        // set login button to log out button
        self.btnLogin.selected = YES;
    }
    else
    {
        // set login button to login button
        self.btnLogin.selected = NO;
    }
    
    // if current user is from facebook, disable login/logout button
    if ([User currentUser].type == UserTypeFacebook)
    {
        self.btnLogin.enabled = NO;
    }

}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Table view data source

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{
    // Return the number of sections.
    return 1;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    // Return the number of rows in the section.
    return 1;
}

/*
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:<#@"reuseIdentifier"#> forIndexPath:indexPath];
    
    // Configure the cell...
    
    return cell;
}
*/

/*
// Override to support conditional editing of the table view.
- (BOOL)tableView:(UITableView *)tableView canEditRowAtIndexPath:(NSIndexPath *)indexPath
{
    // Return NO if you do not want the specified item to be editable.
    return YES;
}
*/

/*
// Override to support editing the table view.
- (void)tableView:(UITableView *)tableView commitEditingStyle:(UITableViewCellEditingStyle)editingStyle forRowAtIndexPath:(NSIndexPath *)indexPath
{
    if (editingStyle == UITableViewCellEditingStyleDelete) {
        // Delete the row from the data source
        [tableView deleteRowsAtIndexPaths:@[indexPath] withRowAnimation:UITableViewRowAnimationFade];
    } else if (editingStyle == UITableViewCellEditingStyleInsert) {
        // Create a new instance of the appropriate class, insert it into the array, and add a new row to the table view
    }   
}
*/

/*
// Override to support rearranging the table view.
- (void)tableView:(UITableView *)tableView moveRowAtIndexPath:(NSIndexPath *)fromIndexPath toIndexPath:(NSIndexPath *)toIndexPath
{
}
*/

/*
// Override to support conditional rearranging of the table view.
- (BOOL)tableView:(UITableView *)tableView canMoveRowAtIndexPath:(NSIndexPath *)indexPath
{
    // Return NO if you do not want the item to be re-orderable.
    return YES;
}
*/

/*
#pragma mark - Navigation

// In a storyboard-based application, you will often want to do a little preparation before navigation
- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender
{
    // Get the new view controller using [segue destinationViewController].
    // Pass the selected object to the new view controller.
}
*/

- (IBAction)onBack:(id)sender
{
    // get values from controls
//    AppSettings *settings = [AppSettings sharedSettings];
//    settings.twitterUser = self.txtUserName.text;
//    settings.twitterPwd = self.txtPwd.text;
//    [settings save];
    
    [self.navigationController popViewControllerAnimated:YES];
}

- (IBAction)onLogin:(id)sender
{
    if (self.btnLogin.selected == YES)
    {
        self.btnLogin.selected = NO;
        [FBSession.activeSession closeAndClearTokenInformation];
    }
    else
    {
        [FBSession openActiveSessionWithReadPermissions:@[@"public_profile", @"email"]
                                           allowLoginUI:YES
                                      completionHandler:^(FBSession *session, FBSessionState state, NSError *error) {
                                          // Handler for session state changes
                                          // This method will be called EACH time the session state changes,
                                          // also for intermediate states and NOT just when the session open
                                          
                                          if (!error && state == FBSessionStateOpen)
                                          {
                                              NSLog(@"Session opened");
                                              [self.btnLogin setSelected:YES];
                                              
                                              [FBRequestConnection startWithGraphPath:@"/me/permissions"
                                                                    completionHandler:^(FBRequestConnection *connection, id result, NSError *error) {
                                                                        //__block NSString *alertText;
                                                                        //__block NSString *alertTitle;
                                                                        if (!error){
                                                                            NSDictionary *permissions= [(NSArray *)[result data] objectAtIndex:0];
                                                                            if (![permissions objectForKey:@"publish_actions"]){
                                                                                // Publish permissions not found, ask for publish_actions
                                                                                [self requestPublishPermissions];
                                                                                
                                                                            } else {
                                                                                // Publish permissions found, publish the OG story
                                                                                //[self publishStory];
                                                                            }
                                                                            
                                                                        } else {
                                                                            // There was an error, handle it
                                                                            // See https://developers.facebook.com/docs/ios/errors/
                                                                        }
                                                                    }];
                                              return;
                                          }

                                         
                                      }];
    }
}

- (void)requestPublishPermissions
{
    // Request publish_actions
    [FBSession.activeSession requestNewPublishPermissions:[NSArray arrayWithObject:@"publish_actions"]
                                          defaultAudience:FBSessionDefaultAudienceOnlyMe
                                        completionHandler:^(FBSession *session, NSError *error) {
                                            __block NSString *alertText;
                                            __block NSString *alertTitle;
                                            if (!error) {
                                                if ([FBSession.activeSession.permissions
                                                     indexOfObject:@"publish_actions"] == NSNotFound){
                                                    // Permission not granted, tell the user we will not publish
                                                    alertTitle = @"Permission not granted";
                                                    alertText = @"Your action will not be published to Facebook.";
                                                    //[[[UIAlertView alloc] initWithTitle:alertTitle                                                                                 message:alertText                                                                                delegate:self                                                                       cancelButtonTitle:@"OK!"                                                                       otherButtonTitles:nil] show];
                                                    NSLog(@"%@", alertText);
                                                } else {
                                                    // Permission granted, publish the OG story
                                                    //[self publishStory];
                                                }
                                                
                                            } else {
                                                // There was an error, handle it
                                                // See https://developers.facebook.com/docs/ios/errors/
                                            }
                                        }];
    
}

@end
