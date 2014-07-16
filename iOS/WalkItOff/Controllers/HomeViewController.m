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
#import "RecordViewController.h"
#import "Model.h"
#import "AppContext.h"
#import "Formulas+walkitoff.h"


@interface HomeViewController () {
    UIBarButtonItem *_addButton;
    
    int _percentageCaloriesBurned;
    float _caloriesToBurn, _totalCalories;
    float _milesToGo, _totalMiles;
    int _stepsToTake, _allSteps;
    
    int _caloriesBurnedToday;
    int _casualPace;
    int _briskPace;
    CGFloat _distance;
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
    
    // set footer view of current table
    UIView *v = [[UIView alloc] initWithFrame:CGRectMake(0, 0, self.view.frame.size.width, 40)];
    UIActivityIndicatorView *indicator = [[UIActivityIndicatorView alloc] initWithFrame:CGRectMake(v.frame.size.width / 2 - 30 / 2.0, 5, 30, 30)];
    [v addSubview:indicator];
    [indicator setColor:[UIColor lightGrayColor]];
    [indicator startAnimating];
    self.tblCurrentFoods.tableFooterView = v;
    
    // load previous values from context
    [self initParamsFromContext];

    
    // get favorite food
#ifdef _USE_REMOTE
    [Food getFavoritesFood:[User currentUser].uid success:^(NSMutableArray *arrayData)
     {
         [User currentUser].favoritesFoods = [[NSMutableArray alloc] initWithArray:arrayData];
         
     } failure:^(NSString *msg) {
         //
     }];
#else
    [Food getFavoritesFoodsWithLocal:[User currentUser].uid success:^(NSMutableArray *arrayData)
     {
         [User currentUser].favoritesFoods = [[NSMutableArray alloc] initWithArray:arrayData];
         
     } failure:^(NSString *msg) {
         //
     }];
#endif

    
    
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)initParamsFromContext
{
    // load temp values from context
    AppContext *context = [AppContext sharedContext];
    
    _totalCalories = context.totalCalories;
    
    // steps taken
    int stepsTaken = (int)context.stepsTaken;
    
    CGFloat userCaloriesBurnedPerStep = [Formulas userCaloriesBurnedPerStep:[Formulas userCaloriesBurnedPerMile:[Formulas weightInLbsWithKg:[User currentUser].weight]] strideLengthInMiles:[Formulas userStrideLengthInMiles:[User currentUser].height]];
    
    // calories burnd
    CGFloat caloriesBurned = stepsTaken * userCaloriesBurnedPerStep;
    if (_totalCalories == 0)
    {
        _percentageCaloriesBurned = 0;
    }
    else
    {
        _percentageCaloriesBurned = caloriesBurned / _totalCalories * 100;
        if (_percentageCaloriesBurned > 100)
            _percentageCaloriesBurned = 100;
    }
    
    // caloreis remaining
    _caloriesToBurn = _totalCalories - caloriesBurned;
    if (_caloriesToBurn < 0)
        _caloriesToBurn = 0;
    
    // all steps for total calories(current foods)
    if (userCaloriesBurnedPerStep == 0)
        _allSteps = 0;
    else
        _allSteps = _totalCalories / userCaloriesBurnedPerStep;
    
    // steps to take
    _stepsToTake = _allSteps - stepsTaken;
    if (_stepsToTake < 0)
        _stepsToTake = 0;
    
    // miles to go
    _milesToGo = _stepsToTake * [Formulas userStrideLengthInMiles:[User currentUser].height];
    _totalMiles = _allSteps * [Formulas userStrideLengthInMiles:[User currentUser].height];
    
    
    _caloriesBurnedToday = (int)context.numberOfTodaySteps * userCaloriesBurnedPerStep;
    if (_totalCalories == 0)
    {
        _casualPace = 0;
        _briskPace = 0;
        _distance = 0;
    }
    else
    {
        _casualPace = _milesToGo / 2.0 * 60;
        _briskPace = _milesToGo / 3.5 * 60;
        _distance = (int)context.numberOfTodaySteps * [Formulas userStrideLengthInMiles:[User currentUser].height];
    }
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
    
    AppDelegate *appDelegate = (AppDelegate*)[UIApplication sharedApplication].delegate;
    appDelegate.pedometerViewerDelegate = self;
    
    // retrieve current food
    [self.tblCurrentFoods.tableFooterView setHidden:NO];
#ifdef _USE_REMOTE
    [Food getCurrentFoods:[User currentUser].uid isConsumed:NO success:^(NSMutableArray *arrayData)
     {
         [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
             // set current foods
             [User currentUser].currentFoods = [[NSMutableArray alloc] initWithArray:arrayData];
             
             [self.tblCurrentFoods reloadData];
             
             [self.tblCurrentFoods.tableFooterView setHidden:YES];
             
             [self recalcParams];
             [self reloadParams];
             [self.tblStatic reloadData];
         }];
         
     } failure:^(NSString *msg) {
         [self.tblCurrentFoods.tableFooterView setHidden:YES];
     }];
#else
    [CurrentFood getCurrentFoodsWithLocal:[User currentUser].uid isConsumed:NO success:^(NSMutableArray *arrayData)
     {
         [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
             // set current foods
             [User currentUser].currentFoods = [[NSMutableArray alloc] initWithArray:arrayData];
             
             [self.tblCurrentFoods reloadData];
             
             [self.tblCurrentFoods.tableFooterView setHidden:YES];
             
             [self recalcParams];
             [self reloadParams];
             [self.tblStatic reloadData];
         }];
         
     } failure:^(NSString *msg) {
         [self.tblCurrentFoods.tableFooterView setHidden:YES];
     }];
#endif

    
}

- (void)viewWillDisappear:(BOOL)animated
{
    [super viewWillDisappear:animated];
    
    AppDelegate *appDelegate = (AppDelegate*)[UIApplication sharedApplication].delegate;
    appDelegate.pedometerViewerDelegate = nil;
}

// re-calculate parameters from total calories, steps taken
- (void)recalcParams
{
    AppContext *context = [AppContext sharedContext];
    
    // total calories
    _totalCalories = 0;
    NSMutableArray *currentFoods = [User currentUser].currentFoods;
    for (Food *food in currentFoods) {
        _totalCalories += food.calories;
    }
    
    // steps taken
    int stepsTaken = (int)context.stepsTaken;
    
    CGFloat userCaloriesBurnedPerStep = [Formulas userCaloriesBurnedPerStep:[Formulas userCaloriesBurnedPerMile:[Formulas weightInLbsWithKg:[User currentUser].weight]] strideLengthInMiles:[Formulas userStrideLengthInMiles:[User currentUser].height]];
    
    // calories burnd
    CGFloat caloriesBurned = stepsTaken * userCaloriesBurnedPerStep;
    
    if (_totalCalories == 0)
        _percentageCaloriesBurned = 0;
    else
    {
        _percentageCaloriesBurned = (caloriesBurned / _totalCalories) * 100;
        if (_percentageCaloriesBurned > 100)
            _percentageCaloriesBurned = 100;
    }
    
    // caloreis to burn
    _caloriesToBurn = _totalCalories - caloriesBurned;
    if (_caloriesToBurn < 0)
        _caloriesToBurn = 0;
    
    // all steps for total calories(current foods)
    _allSteps = _totalCalories / userCaloriesBurnedPerStep;
    
    // steps taken
    _stepsToTake = _allSteps - stepsTaken;
    if (_stepsToTake < 0)
        _stepsToTake = 0;
    
    // miles to go
    _milesToGo = _stepsToTake * [Formulas userStrideLengthInMiles:[User currentUser].height];
    _totalMiles = _allSteps * [Formulas userStrideLengthInMiles:[User currentUser].height];
    
    // save this vales to context
    context.percentageCaloriesBurned = _percentageCaloriesBurned;
    context.totalCalories = _totalCalories;
    context.caloriesToBurn = _caloriesToBurn;
    [context save];
    
    _caloriesBurnedToday = (int)context.numberOfTodaySteps * userCaloriesBurnedPerStep;
    if (_totalCalories == 0)
    {
        _casualPace = 0;
        _briskPace = 0;
        _distance = 0;
    }
    else
    {
        _casualPace = _milesToGo / 2.0 * 60;
        _briskPace = _milesToGo / 3.5 * 60;
        _distance = (int)context.numberOfTodaySteps * [Formulas userStrideLengthInMiles:[User currentUser].height];
    }
}

- (void)reloadParams
{
    self.lblCasualPace.text = [NSString stringWithFormat:@"Esimate Time To Walk It Off At Casual Pace - %d:%d", _casualPace / 60, _casualPace % 60];
    self.lblBriskPace.text = [NSString stringWithFormat:@"Estimate Time To Walk It Off At Brisk Pace - %d:%d", _briskPace / 60, _briskPace % 60];
    self.lblCalories.text = [NSString stringWithFormat:@"Calories Burned Today - %d", (int)_caloriesBurnedToday];
    self.lblSteps.text = [NSString stringWithFormat:@"Steps Taken Today - %d", (int)[AppContext sharedContext].numberOfTodaySteps];
    self.lblDistance.text = [NSString stringWithFormat:@"Distance Walked Today - %.1f Miles", _distance];
}

#pragma mark table view data source

- (NSMutableArray *)dataForTable:(UITableView *)tableView
{
    return [User currentUser].currentFoods;
}

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
        return [self dataForTable:tableView].count;
}

static UITableViewCell *_prototypeHomeStaticCell = nil;
static UITableViewCell *_prototypeHomeCurrentCell = nil;

- (UITableViewCell *)prototypeHomeStaticCell
{
    if (_prototypeHomeStaticCell == nil)
        _prototypeHomeStaticCell = [self.tblStatic dequeueReusableCellWithIdentifier:@"cellidentifier"];
    return _prototypeHomeStaticCell;
}

- (UITableViewCell *)prototypeHomeCurrentCell
{
    if (_prototypeHomeCurrentCell == nil)
        _prototypeHomeCurrentCell = [self.tblCurrentFoods dequeueReusableCellWithIdentifier:@"cellidentifier"];
    return _prototypeHomeCurrentCell;
}

- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    if (tableView == self.tblStatic)
        return [self prototypeHomeStaticCell].frame.size.height;
    else
    {
        NSMutableArray *arrayData = [self dataForTable:tableView];
        // calculate height from data;
        if (arrayData.count > indexPath.row)
        {
#if false
            UILabel *lblName = (UILabel *)[[self prototypeHomeCurrentCell] viewWithTag:101];
            Food *food = (Food *)[arrayData objectAtIndex:indexPath.row];
            CGRect labelRect = [food.name
                                boundingRectWithSize:CGSizeMake(lblName.frame.size.width, 500)
                                options:NSStringDrawingUsesLineFragmentOrigin
                                attributes:@{
                                             NSFontAttributeName : lblName.font
                                             }
                                context:nil];
            return ceilf(labelRect.size.height) + 4 /* top margin */ + 4 /* bottom margin */;
#else
            return [self prototypeHomeCurrentCell].frame.size.height;
#endif
        }
        else
            return 0.0;
    }
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
            label.text = [NSString stringWithFormat:@"%d%% of Calories Burned", _percentageCaloriesBurned];
            [self setImageProgress:_percentageCaloriesBurned back:imgProgressBack bar:imgProgressBar];
        }
        else if (indexPath.row == 1)
        {
            label.text = [NSString stringWithFormat:@"%d Calories To Burn", (int)_caloriesToBurn];
            if (_totalCalories != 0)
            {
                CGFloat percentage = (_totalCalories - _caloriesToBurn) / _totalCalories * 100;
                
                [self setImageProgress:(int)percentage back:imgProgressBack bar:imgProgressBar];
            }
            else
                [self setImageProgress:0 back:imgProgressBack bar:imgProgressBar];
        }
        else if (indexPath.row == 2)
        {
            label.text = [NSString stringWithFormat:@"%.1f Miles To Go", _milesToGo];
            if (_totalMiles != 0)
                [self setImageProgress:(int)((CGFloat)(_totalMiles - _milesToGo) / _totalMiles * 100) back:imgProgressBack bar:imgProgressBar];
            else
                [self setImageProgress:0 back:imgProgressBack bar:imgProgressBar];
        }
        else if (indexPath.row == 3)
        {
            label.text = [NSString stringWithFormat:@"%d Steps To Take", _stepsToTake];
            if (_allSteps != 0)
                [self setImageProgress:(int)((CGFloat)(_allSteps - _stepsToTake) / _allSteps * 100) back:imgProgressBack bar:imgProgressBar];
            else
                [self setImageProgress:0 back:imgProgressBack bar:imgProgressBar];
        }
        
        return cell;
    }
    else
    {
        NSMutableArray *arrayData = [self dataForTable:tableView];
        UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:@"cellidentifier"];
        
        UILabel *label = (UILabel *)[cell viewWithTag:101]; // name of food
        [label setNumberOfLines:0];
        if (arrayData.count > indexPath.row)
        {
            Food *food = (Food *)[arrayData objectAtIndex:indexPath.row];
            [label setText:food.name];
            //[cell.contentView layoutIfNeeded];
        }
        
        return cell;
    }
}

- (void)setImageProgress:(int)percentage back:(UIImageView *)back bar:(UIImageView *)bar
{
    CGRect frame = back.frame;
    frame.size.width = frame.size.width * percentage / 100.0;
    bar.frame = frame;
}

#pragma mark table view delegate

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    if (tableView == self.tblStatic)
    {
        if (indexPath.row == 0 ||
            indexPath.row == 1)
        {
            FoodsViewController *vc = [self.storyboard instantiateViewControllerWithIdentifier:@"FoodsViewController"];
            [vc initDisplayMode:DisplayModeCurrent];
            [self.navigationController pushViewController:vc animated:YES];
            
        }
        else if (indexPath.row == 2 ||
                 indexPath.row == 3)
        {
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
    if (scrollView == self.swipeView)
    {
        CGFloat width = scrollView.frame.size.width;
        NSInteger page = (scrollView.contentOffset.x + (0.5f * width)) / width;
        
        self.pageCtrl.currentPage = page;
        
        [self.tblCurrentFoods flashScrollIndicators];
    }
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

#pragma mark - PedometerViewerDelegate
- (void)updateNumberOfSteps:(NSInteger)numberOfSteps
{
    [self recalcParams];
    [self reloadParams];
    [self.tblStatic reloadData];
}

- (void)consumedCurrentFoods:(NSInteger)stepsTaken withDate:(NSDate *)date
{
    [self recalcParams];
    [self reloadParams];
    [self.tblStatic reloadData];
}

@end
