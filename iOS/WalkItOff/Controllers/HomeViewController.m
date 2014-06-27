//
//  HomeViewController.m
//  WalkItOff
//
//  Created by Donald Pae on 6/10/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "HomeViewController.h"
#import "UIManager.h"
#import "MainTabController.h"
#import "FoodsViewController.h"
#import "TestViewController.h"
#import "RecordViewController.h"


@interface HomeViewController () {
    NSMutableArray *currFoods;
    UIBarButtonItem *_addButton;
}

@property (nonatomic, strong) IBOutlet UITableView *tblStatic;
@property (nonatomic, strong) IBOutlet UITableView *tblCurrentFoods;
@property (nonatomic, strong) IBOutlet UIScrollView *swipeView;

@property (nonatomic, strong) IBOutlet UILabel *lblCasualPace;
@property (nonatomic, strong) IBOutlet UILabel *lblBriskPace;
@property (nonatomic, strong) IBOutlet UILabel *lblCalories;
@property (nonatomic, strong) IBOutlet UILabel *lblSteps;
@property (nonatomic, strong) IBOutlet UILabel *lblDistance;

@property (nonatomic, strong) IBOutlet UIPageControl *pageCtrl;

@property (nonatomic, strong) IBOutlet UIView *firstView;
@property (nonatomic, strong) IBOutlet UIView *secondView;

@end

@implementation HomeViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    // Do any additional setup after loading the view.
    
    self.navigationController.navigationBarHidden = NO;
    
    currFoods = [[NSMutableArray alloc] init];
    
    
    [self.firstView mas_makeConstraints:^(MASConstraintMaker *make)
     {
         make.left.equalTo(self.swipeView);
         make.top.equalTo(self.swipeView);
         make.bottom.equalTo(self.swipeView);
         make.width.equalTo(@(320));
         make.height.equalTo(@(163));
     }];
    
    [self.secondView mas_makeConstraints:^(MASConstraintMaker *make){
        make.left.equalTo(self.firstView.mas_right);
        make.width.equalTo(@(320));
        make.top.equalTo(self.swipeView);
        make.height.equalTo(@(163));
    }];
    
    [self.tblCurrentFoods mas_makeConstraints:^(MASConstraintMaker *make){
        make.left.equalTo(self.secondView.mas_right);
        make.right.equalTo(self.swipeView);
        make.width.equalTo(@(320));
        make.height.equalTo(@(163));
    }];
    
    // add button
    _addButton = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemAdd target:self action:@selector(addPressed)];
    self.navigationItem.rightBarButtonItem = _addButton;
    
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

/*
#pragma mark - Navigation

// In a storyboard-based application, you will often want to do a little preparation before navigation
- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender
{
    // Get the new view controller using [segue destinationViewController].
    // Pass the selected object to the new view controller.
}
*/

- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    
    UIManager *uimanager = [UIManager sharedUIManager];
    
    self.navigationItem.title = [uimanager appTitle];
    self.navigationController.navigationBar.barStyle = [uimanager navbarStyle];
    self.navigationController.navigationBar.tintColor = [uimanager navbarTintColor];
    self.navigationController.navigationBar.titleTextAttributes = [uimanager navbarTitleTextAttributes];
    self.navigationController.navigationBar.barTintColor = [uimanager navbarBarTintColor];
    
    
    
    [self.tblStatic reloadData];
    
}

#pragma mark table view data source

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{
    if (tableView == self.tblStatic)
        return 1;
    else
        return 1;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    if (tableView == self.tblStatic)
        return 4;
    else
        return currFoods.count;
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    if (tableView == self.tblStatic)
    {
        UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:@"cellidentifier"];
        
        UILabel *label = (UILabel *)[cell viewWithTag:101];
        UIImageView *imgProgressBack = (UIImageView *)[cell viewWithTag:102];
        UIImageView *imgProgressBar = (UIImageView *)[cell viewWithTag:103];
        
        if (indexPath.row == 0)
        {
            label.text = [NSString stringWithFormat:@"%d%% of Calories Burned", 80];
        }
        else if (indexPath.row == 1)
        {
            label.text = [NSString stringWithFormat:@"%d Calories To Burn", 720];
        }
        else if (indexPath.row == 2)
        {
            label.text = [NSString stringWithFormat:@"%d Miles To Go", 3];
        }
        else if (indexPath.row == 3)
        {
            label.text = [NSString stringWithFormat:@"%d Steps To Take", 220];
        }
        
        return cell;
    }
    else
    {
        UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:@"cellidentifier"];
        
        UILabel *label = (UILabel *)[cell viewWithTag:101]; // name of food
        
        return cell;
    }
}

#pragma mark table view delegate

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    if (tableView == self.tblStatic)
    {
        if (indexPath.row == 0 ||
            indexPath.row == 1)
        {
            /*
            CombinedViewController *vc = [self.storyboard instantiateViewControllerWithIdentifier:@"CombinedViewController"];
            [vc initDisplayMode:DisplayModeCurrent];
            [self.navigationController pushViewController:vc animated:YES];
             */

            
            FoodsViewController *vc = [self.storyboard instantiateViewControllerWithIdentifier:@"FoodsViewController"];
            [vc initDisplayMode:DisplayModeCurrent];
            [self.navigationController pushViewController:vc animated:YES];
             
            /*
            TestViewController *vc = [self.storyboard instantiateViewControllerWithIdentifier:@"TestViewController"];
            [self.navigationController pushViewController:vc animated:YES];
             */
            
            
        }
        else if (indexPath.row == 2 ||
                 indexPath.row == 3)
        {
//            CombinedViewController *vc = [self.storyboard instantiateViewControllerWithIdentifier:@"CombinedViewController"];
//            [vc initDisplayMode:DisplayModeCurrent];
//            [self.navigationController pushViewController:vc animated:YES];
            
            RecordViewController *vc = [self.storyboard instantiateViewControllerWithIdentifier:@"RecordViewController"];
            [self.navigationController pushViewController:vc animated:YES];
        }
    }
    else
    {
        //
    }
}

#pragma mark - Page Scrolling
- (void)scrollViewDidScroll:(UIScrollView *)scrollView
{
    CGFloat width = scrollView.frame.size.width;
    NSInteger page = (scrollView.contentOffset.x + (0.5f * width)) / width;
    
    self.pageCtrl.currentPage = page;
}

- (IBAction)onPageCtrl:(id)sender
{
#if false
    CGRect frame = self.swipeView.frame;
    int currPage = self.pageCtrl.currentPage;
    frame.origin.x = frame.size.width * currPage;
    frame.origin.y = 0;
    [self.swipeView scrollRectToVisible:frame animated:YES];
#endif
}

#pragma mark - navigation item actions

-(void)addPressed {
    /*
     if(_displayMode == DisplayModePeople){
     [self startAddPerson];
     } else{
     [self startAddTopic];
     }
     */
    
    FoodsViewController *vc = [self.storyboard instantiateViewControllerWithIdentifier:@"FoodsViewController"];
    [vc initDisplayMode:DisplayModeFoods];
    [self.navigationController pushViewController:vc animated:YES];
}

@end
