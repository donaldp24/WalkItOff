//
//  TwitterSettingsViewController.m
//  WalkItOff
//
//  Created by Donald Pae on 6/29/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "TwitterSettingsViewController.h"
#import "UIManager.h"
#import "TwitterLoginSettingsViewController.h"
#import "AppSettings.h"

@interface TwitterSettingsViewController () {
    UIBarButtonItem *_backButton;
}

@property (nonatomic, strong) IBOutlet UISwitch *tweetWhenAllCalories;
@property (nonatomic, strong) IBOutlet UISwitch *tweetPer500;

@end

@implementation TwitterSettingsViewController

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
    
    // set values to controls
    AppSettings *settings = [AppSettings sharedSettings];
    [self.tweetWhenAllCalories setOn:settings.isTweetWhenAllCalories];
    [self.tweetPer500 setOn:settings.isTweetPer500];

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
    return 3;
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
    if (indexPath.row == 0)
    {
        TwitterLoginSettingsViewController *vc = [self.storyboard instantiateViewControllerWithIdentifier:@"TwitterLoginSettingsViewController"];
        [self.navigationController pushViewController:vc animated:YES];
    }
}

- (IBAction)onChange:(id)sender
{
    // get changes
    AppSettings *settings = [AppSettings sharedSettings];
    settings.isTweetWhenAllCalories = self.tweetWhenAllCalories.isOn;
    settings.isTweetPer500 = self.tweetPer500.isOn;
    [settings save];
}

#pragma mark - navigation item
- (void)onBack:(id)sender {
    [self.navigationController popViewControllerAnimated:YES];
}

@end
