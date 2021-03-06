//
//  SettingsViewController.m
//  WalkItOff
//
//  Created by Donald Pae on 6/16/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "SettingsViewController.h"
#import "UIManager.h"
#import "AppDelegate.h"
#import "AppContext.h"
#import "UserContext.h"

@interface SettingsViewController ()

@end

@implementation SettingsViewController

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
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    
    UIManager *uimanager = [UIManager sharedUIManager];
    
    self.navigationItem.title = [uimanager appTitle];
    self.navigationController.navigationBar.barStyle = [uimanager navbarStyle];
    self.navigationController.navigationBar.tintColor = [uimanager navbarTintColor];
    self.navigationController.navigationBar.titleTextAttributes = [uimanager navbarTitleTextAttributes];
    self.navigationController.navigationBar.barTintColor = [uimanager navbarBarTintColor];
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
    return 5;
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

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    switch (indexPath.row) {
        case 0:
            [self performSegueWithIdentifier:@"toAccount" sender:self];
            break;
            
        case 1:
            [self performSegueWithIdentifier:@"toTwitter" sender:self];
            break;
            
        case 2:
            [self performSegueWithIdentifier:@"toFacebook" sender:self];
            break;
            
        case 3:
            [self performSegueWithIdentifier:@"toApplication" sender:self];
            break;
            
        case 4:
            [self logout];
            break;
            
        default:
            break;
    }
}

- (void)logout
{
    // stop pedometer
    AppContext *context = [AppContext sharedContext];
    if (context.pedometerStarted)
    {
        AppDelegate *appDelegate = (AppDelegate *)[UIApplication sharedApplication].delegate;
        [appDelegate.pedometer stop];
    }
    
    [UserContext sharedContext].isLoggedIn = NO;
    [UserContext clearDefaultLogin];
    
    if ([User currentUser].type == UserTypeFacebook)
        [FBSession.activeSession closeAndClearTokenInformation];
    
    [self.tabBarController.navigationController popToRootViewControllerAnimated:YES];
}

@end
