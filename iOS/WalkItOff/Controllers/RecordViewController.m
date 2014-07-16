//
//  RecordViewController.m
//  WalkItOff
//
//  Created by Donald Pae on 6/16/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "RecordViewController.h"
#import "NSDate+convenience.h"
#import "UIManager.h"
#import "Model.h"
#import "Formulas+walkitoff.h"
#import "NSDate+walkitoff.h"
#import "FoodInfoViewController.h"
#import "AppContext.h"

@interface RecordViewController () {
    UIBarButtonItem *_backButton;
    NSMutableArray *currentFoods;
    
    int _stepsTaken;
    CGFloat _caloriesBurned;
    CGFloat _milesWalked;
}

@property (nonatomic, strong) IBOutlet UIScrollView *swipeView;
@property (nonatomic, strong) IBOutlet UIView *firstView;
@property (nonatomic, strong) IBOutlet UIView *secondView;

@property (nonatomic, strong) IBOutlet UIPageControl *pageCtrl;

@property (nonatomic, strong) IBOutlet UILabel *lblCaloriesBurned;
@property (nonatomic, strong) IBOutlet UILabel *lblStepsTaken;
@property (nonatomic, strong) IBOutlet UILabel *lblMilesWalked;

@property (nonatomic, strong) IBOutlet UITableView *tblCurrent;

@end

@implementation RecordViewController

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
    
    self.navigationItem.hidesBackButton = YES;
    self.navigationItem.title = [[UIManager sharedUIManager] appTitle];
    
   
    // back button
    _backButton = [[UIBarButtonItem alloc] initWithImage:[UIImage imageNamed:@"backicon"] style:UIBarButtonItemStylePlain target:self action:@selector(onBack:)];
    self.navigationItem.leftBarButtonItem = _backButton;
    
    VRGCalendarView *calendar = [[VRGCalendarView alloc] init];
    calendar.delegate=self;
    calendar.animType = CalendarAnimateHorizontal;
    calendar.selectedDate = [NSDate date];
    [self.view addSubview:calendar];
    
    CGRect rt = self.view.frame;
    [self.firstView mas_makeConstraints:^(MASConstraintMaker *make) {
        make.left.equalTo(self.swipeView);
        make.top.equalTo(self.swipeView);
        make.width.equalTo(@(rt.size.width));
        make.bottom.equalTo(self.swipeView);
    }];
    
    [self.secondView mas_makeConstraints:^(MASConstraintMaker *make) {
        make.left.equalTo(self.firstView.mas_right);
        make.top.equalTo(self.swipeView);
        make.right.equalTo(self.swipeView);
        make.bottom.equalTo(self.swipeView);
        make.width.equalTo(@(rt.size.width));
    }];

    // get parameters with today
    // calories consume on this day
    // calores burned on this day
    // steps taken on this day
    // miles walked on this day
    [self getStepsTakenCaloriesBurnedWithDate:[NSDate date]];
    
    // get foods with today
    currentFoods = [[NSMutableArray alloc] init];
    
    UIView *v = [[UIView alloc] initWithFrame:CGRectMake(0, 0, self.view.frame.size.width, 30)];
    UIActivityIndicatorView *indicator = [[UIActivityIndicatorView alloc] initWithFrame:CGRectMake(v.frame.size.width / 2 - 20 / 2, 5, 20, 20)];
    [v addSubview:indicator];
    [indicator startAnimating];
    [indicator setColor:[UIColor lightGrayColor]];
    
    self.tblCurrent.tableFooterView = v;
    
    [self getCurrentFoodsWithDate:[NSDate date]];

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

- (void)getCurrentFoodsWithDate:(NSDate *)date
{
    [self.tblCurrent.tableFooterView setHidden:NO];
    
#ifdef _USE_REMOTE
    [Food getCurrentFoods:[User currentUser].uid withDate:date success:^(NSMutableArray *arrayData) {
        [[NSOperationQueue mainQueue] addOperationWithBlock: ^() {
            self.tblCurrent.tableFooterView.hidden = YES;
            currentFoods = [[NSMutableArray alloc] initWithArray:arrayData];
            [self.tblCurrent reloadData];
        }];
    } failure:^(NSString *msg) {
        [[NSOperationQueue mainQueue] addOperationWithBlock:^(){
            self.tblCurrent.tableFooterView.hidden = YES;
        }];
    }];
#else
    [CurrentFood getCurrentFoodsWithLocal:[User currentUser].uid withDate:date success:^(NSMutableArray *arrayData) {
        [[NSOperationQueue mainQueue] addOperationWithBlock: ^() {
            self.tblCurrent.tableFooterView.hidden = YES;
            currentFoods = [[NSMutableArray alloc] initWithArray:arrayData];
            [self.tblCurrent reloadData];
        }];
    } failure:^(NSString *msg) {
        [[NSOperationQueue mainQueue] addOperationWithBlock:^(){
            self.tblCurrent.tableFooterView.hidden = YES;
        }];
    }];
#endif
}

- (void)getStepsTakenCaloriesBurnedWithDate:(NSDate *)date
{
    
    if ([date compareOnlyDate:[NSDate date]] == NSOrderedSame)
    {
        // today
        
        _stepsTaken = [AppContext sharedContext].numberOfTodaySteps;
        
        // calories burned
        //      weight has to changed to weight In Lbs
        _caloriesBurned = _stepsTaken * [Formulas userCaloriesBurnedPerStep:[Formulas userCaloriesBurnedPerMile:[User currentUser].weight] strideLengthInMiles:[Formulas userStrideLengthInMiles:[User currentUser].height]];
        
        // miles walked
        _milesWalked = [Formulas distanceWalked:[Formulas userStrideLengthInMiles:[User currentUser].height] numberOfSteps:_stepsTaken];
        
        [self reloadParams];
        
    }
    else
    {
#ifdef _USE_REMOTE
        [Consumed getConsumed:[User currentUser].uid withDate:date success:^(Consumed *consumed) {
            [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
                
               
                self.lblStepsTaken.text = [NSString stringWithFormat:@"%d", consumed.stepsTaken];
                self.lblCaloriesBurned.text = [NSString stringWithFormat:@"%d", (int)consumed.caloriesConsumed];
                self.lblMilesWalked.text = [NSString stringWithFormat:@"%d", (int)consumed.milesWalked];
            }];
        } failure:^(NSString *msg) {
            [[NSOperationQueue mainQueue] addOperationWithBlock:^{
                self.lblStepsTaken.text = @"";
                self.lblCaloriesBurned.text = @"";
                self.lblMilesWalked.text = @"";
            }];
        }];
#else
        [Consumed getConsumedWithLocal:[User currentUser].uid withDate:date success:^(Consumed *consumed) {
            [[NSOperationQueue mainQueue] addOperationWithBlock:^() {

                _stepsTaken = consumed.stepsTaken;
                _caloriesBurned = consumed.caloriesConsumed;
                _milesWalked = consumed.milesWalked;
                
                [self reloadParams];
            }];
        } failure:^(NSString *msg) {
            [[NSOperationQueue mainQueue] addOperationWithBlock:^{
                _stepsTaken = 0;
                _caloriesBurned = 0;
                _milesWalked = 0;
                
                [self reloadParams];
            }];
        }];
#endif
        
        
        
    }
    
}

- (void)reloadParams
{
    self.lblStepsTaken.text = [NSString stringWithFormat:@"Steps Taken On This Day - %d", _stepsTaken];
    self.lblCaloriesBurned.text = [NSString stringWithFormat:@"Calories Consumed On This Day - %d", (int)_caloriesBurned];
    self.lblMilesWalked.text = [NSString stringWithFormat:@"Miles Walked On This Day - %.1f", _milesWalked];
}

- (void)onBack:(id)sender {
    [self.navigationController popViewControllerAnimated:YES];
}

#pragma mark - Calendar delegate

-(void)calendarView:(VRGCalendarView *)calendarView switchedToMonth:(int)month targetHeight:(float)targetHeight animated:(BOOL)animated {
    if (month==[[NSDate date] month]) {
        NSDate *today = [NSDate date];
        NSArray *dates = [NSArray arrayWithObjects:[NSNumber numberWithInt:[today day]], nil];
        [calendarView markDates:dates];
    }
}

- (void)calendarView:(VRGCalendarView *)calendarView dateSelected:(NSDate *)date
{
    NSLog(@"Selected date = %@",date);
    
    if ([date compare:[NSDate date]] == NSOrderedAscending)
    {
        // refresh parameters
        [self getStepsTakenCaloriesBurnedWithDate:date];
        
        // refresh current foods
        [self getCurrentFoodsWithDate:date];
    }
    else
    {
        [[NSOperationQueue mainQueue] addOperationWithBlock:^(){
            _milesWalked = 0;
            _stepsTaken = 0;
            _caloriesBurned = 0;
            [self reloadParams];
            
            [currentFoods removeAllObjects];
            [self.tblCurrent reloadData];
        }];
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
    CGRect frame = self.swipeView.frame;
    int currPage = self.pageCtrl.currentPage;
    frame.origin.x = frame.size.width * currPage;
    frame.origin.y = 0;
    [self.swipeView scrollRectToVisible:frame animated:YES];
}

#pragma mark - Table view data source
static UITableViewCell *_prototypeRecordCurrentCell = nil;

- (UITableViewCell *)prototypeRecordCurrentCell
{
    if (_prototypeRecordCurrentCell == nil)
        _prototypeRecordCurrentCell = [self.tblCurrent dequeueReusableCellWithIdentifier:@"cellidentifier"];
    return _prototypeRecordCurrentCell;
}

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{
    return 1;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    return [currentFoods count];
}
#if 0
- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    Food *food = [currentFoods objectAtIndex:indexPath.row];
    
    UILabel *lblName = (UILabel *)[[self prototypeRecordCurrentCell] viewWithTag:101];
    CGRect labelRect = [food.name
                        boundingRectWithSize:CGSizeMake(lblName.frame.size.width, 500)
                        options:NSStringDrawingUsesLineFragmentOrigin
                        attributes:@{
                                     NSFontAttributeName : lblName.font
                                     }
                        context:nil];
    return ceilf(labelRect.size.height) + 4 /* top */ + 4 /* bottom */;
}
#endif

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    Food *food = [currentFoods objectAtIndex:indexPath.row];
    
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:@"cellidentifier"];
    UILabel *lblName = (UILabel *)[cell viewWithTag:101];
    [lblName setNumberOfLines:0];
    [lblName setText:food.name];
    
    return cell;
}

#pragma mark - Table view delegate
- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    Food *food = [currentFoods objectAtIndex:indexPath.row];
    
    // go to food info view
    FoodInfoViewController *vc = [self.storyboard instantiateViewControllerWithIdentifier:@"FoodInfoViewController"];
    vc.food = food;
    [self.navigationController pushViewController:vc animated:YES];
}

@end
