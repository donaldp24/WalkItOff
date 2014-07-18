//
//  FoodsViewController.m
//  WalkItOff
//
//  Created by Donald Pae on 6/15/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "FoodsViewController.h"
#import "UIManager.h"
#import "FoodTableViewCell.h"
#import "CurrentTableViewCell.h"
#import "FavoritesTableViewCell.h"
#import "CustomSegmentedControl.h"
#import "AddFoodViewController.h"
#import "FoodInfoViewController.h"
#import "AppContext.h"
#import "AutoMessageBox.h"

#import "Food.h"
#import "SVProgressHUD+walkitoff.h"

static NSString *FoodsCellIdentifier = @"FoodsCell";
static NSString *CurrentCellIdentifier = @"CurrentCell";
static NSString *FavoritesCellIdentifier = @"FavoritesCell";

static NSString *FOODS_TITLE = @"Library";
static NSString *CURRENT_TITLE = @"Currents";
static NSString *FAVORITES_TITLE = @"Favorites";


static NSUInteger FOODS_SEGMENT_INDEX = 0;
static NSUInteger CURRENT_SEGMENT_INDEX = 1;
static NSUInteger FAVORITES_SEGMENT_INDEX = 2;


@interface FoodsViewController () {
    UIBarButtonItem *_addButton;
    UIBarButtonItem *_backButton;
    UIResponder *currentResponder;
    
    NSString *_keyword;
    BOOL _pendingNormal;
    BOOL _pendingSearching;
    
    BOOL _nextPageNormal;
    BOOL _nextPageSearching;
    
    BOOL _searching;
    
    BOOL _hasNextNormal;
    BOOL _hasNextSearching;
    
    BOOL firstLoaded;
    
    CGFloat _totalCalories;
    CGFloat _caloriesBurned;
}
@property (nonatomic, strong) IBOutlet UIImageView *imgView;
@property (nonatomic, strong) IBOutlet UIScrollView *swipeView;

@property (nonatomic, strong) IBOutlet UITableView *tblFoods;
@property (nonatomic, strong) IBOutlet UITableView *tblCurrents;
@property (nonatomic, strong) IBOutlet UITableView *tblFavorites;
@property (nonatomic, strong) IBOutlet UILabel *lblFoods;
@property (nonatomic, strong) IBOutlet UILabel *lblCurrents;
@property (nonatomic, strong) IBOutlet UILabel *lblFavorites;

@property (nonatomic, strong) IBOutlet UIImageView *progress_back;
@property (nonatomic, strong) IBOutlet UIImageView *progressbar;
@property (nonatomic, strong) IBOutlet UILabel *lblCalories;

@property (nonatomic, strong) NSMutableArray *foods;

@property (nonatomic, strong) NSMutableArray *foodsSearchResults;

@property (nonatomic, strong) IBOutlet UITextField *txtKeyword;
@property (nonatomic, strong) IBOutlet UIButton *btnSearchCancel;


@property (nonatomic, strong) CustomSegmentedControl* segmentedControl;
@property (nonatomic, assign) DisplayMode displayMode;
@property (nonatomic, assign) DisplayMode oldDisplayMode;
@property (nonatomic, assign) BOOL transitioningData;


@property (strong, nonatomic) UIRefreshControl *refresh;

@end

@implementation FoodsViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}


- (void)initDisplayMode:(DisplayMode) mode
{
    _displayMode = mode;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    // Do any additional setup after loading the view.
    
    CGRect rt = self.view.frame;
    
    [self.swipeView mas_makeConstraints:^(MASConstraintMaker *make) {
        make.edges.equalTo(self.view);
    }];
    
    // foods
    [self.lblFoods mas_makeConstraints:^(MASConstraintMaker *make) {
        make.top.equalTo(self.swipeView);
        make.left.equalTo(self.swipeView);
        make.width.equalTo(@(rt.size.width));
    }];
    
    [self.tblFoods mas_makeConstraints:^(MASConstraintMaker *make) {
        make.left.equalTo(self.swipeView);
        make.top.equalTo(self.lblFoods.mas_bottom);
        make.width.equalTo(self.lblFoods);
        make.bottom.equalTo(self.swipeView);
    }];
    
    // currents
    [self.lblCurrents mas_makeConstraints:^(MASConstraintMaker *make) {
        make.left.equalTo(self.lblFoods.mas_right);
        make.width.equalTo(@(rt.size.width));
        make.top.equalTo(self.swipeView);
    }];
    
    
    // favorites
    [self.lblFavorites mas_makeConstraints:^(MASConstraintMaker *make) {
        make.left.equalTo(self.lblCurrents.mas_right);
        make.width.equalTo(@(rt.size.width));
        make.right.equalTo(self.swipeView);
        make.top.equalTo(self.swipeView);
    }];
    
    [self.tblFavorites mas_makeConstraints:^(MASConstraintMaker *make) {
        make.top.equalTo(self.lblFavorites);
        make.right.equalTo(self.swipeView);
        make.bottom.equalTo(self.swipeView);
    }];
    
    
    self.swipeView.backgroundColor = [UIManager appBackgroundColor];
    self.lblFoods.backgroundColor = [UIManager appBackgroundColor];
    self.lblCurrents.backgroundColor = [UIManager appBackgroundColor];
    self.lblFavorites.backgroundColor = [UIManager appBackgroundColor];

    // init models
    self.foods = [[NSMutableArray alloc] init];
    
    self.foodsSearchResults = [[NSMutableArray alloc] init];

    
    //[self initTempData];
    /////
    
    self.navigationItem.hidesBackButton = YES;
    self.navigationItem.title = FOODS_TITLE;
    
    // add button
    _addButton = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemAdd target:self action:@selector(addPressed:)];
    self.navigationItem.rightBarButtonItem = _addButton;
    
    // back button
    
    _backButton = [[UIBarButtonItem alloc] initWithImage:[UIImage imageNamed:@"backicon"] style:UIBarButtonItemStylePlain target:self action:@selector(onBack:)];
    self.navigationItem.leftBarButtonItem = _backButton;
    
    
    if ([self respondsToSelector:@selector(setAutomaticallyAdjustsScrollViewInsets:)])
        self.automaticallyAdjustsScrollViewInsets = YES;
    
    [self setupSegmentControl];
    
  
    // indicator view
    CGRect rtFoods = self.tblFoods.frame;
    UIView *v = [[UIView alloc] initWithFrame:CGRectMake(0, 0, rtFoods.size.width, 40)];
    v.backgroundColor = [UIColor whiteColor];
    UIActivityIndicatorView *indicator = [[UIActivityIndicatorView alloc] initWithFrame:CGRectMake(rtFoods.size.width / 2 - 30 / 2, 5, 30, 30)];
    [v addSubview:indicator];
    [indicator setColor:[UIColor blackColor]];
    [indicator startAnimating];
    
    self.tblFoods.tableFooterView = v;
    self.tblFoods.tableFooterView.hidden = NO;
    
    UIView *emptyView = [[UIView alloc] initWithFrame:CGRectZero];
    self.tblCurrents.tableFooterView = emptyView;
    self.tblFavorites.tableFooterView = emptyView;
    
    
    firstLoaded = YES;
    
    // setup gesture
    UITapGestureRecognizer *tap = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(backgroundTap:)];
    [self.view addGestureRecognizer:tap];
    tap.delegate = self;
    
    // get foods ------------------------------------------------
    _keyword = @"";
    _searching = NO;
    _nextPageNormal = 0;
    _nextPageSearching = 0;
    _hasNextNormal = YES;
    _hasNextSearching = YES;
    

    [Food getFoodsWithLocal:[User currentUser].uid keyword:@"" page:0 success:^(NSMutableArray *foods) {
        self.foods = foods;
    } failure:^(NSString *msg) {
        //
    }];
    
    self.tblFoods.tableFooterView.hidden = NO;
    
    _pendingNormal = YES;
    [Food getFoodsWithRemote:0 keyword:_keyword page:_nextPageNormal success:^(NSMutableArray *arrayData, BOOL hasNext){
        
        [[NSOperationQueue mainQueue] addOperationWithBlock:^(){
            
            _pendingNormal = NO;
            _hasNextNormal = hasNext;
            
            self.tblFoods.tableFooterView.hidden = YES;
            
            _nextPageNormal++;
            [self.foods addObjectsFromArray:arrayData];
            [self.tblFoods reloadData];
            
        }];
        
    } failure:^(NSString *msg) {
        [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
            _pendingNormal = NO;
            self.tblFoods.tableFooterView.hidden = YES;
        }];
    }];
// 
//    // get current foods ------------------------------------------------
//    [Food getCurrentFoods:[User currentUser].uid isConsumed:NO success:^(NSMutableArray *arrayData)
//     {
//         [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
//             [self setDataForCurrentFoods:arrayData];
//             [self.tblCurrents reloadData];
//             [self refreshProgress];
//             
//             [self.tblCurrents.tableFooterView setHidden:YES];
//         }];
//     } failure:^(NSString *msg) {
//         [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
//             [self.tblCurrents.tableFooterView setHidden:YES];
//         }];
//     }];
//    
//    // get favorite foods ------------------------------------------
//    [Food getFavoritesFood:[User currentUser].uid success:^(NSMutableArray *arrayData)
//     {
//         [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
//             [self setDataForFavoritesFoods:arrayData];
//             [self.tblFavorites reloadData];
//             
//             [self.tblFavorites.tableFooterView setHidden:YES];
//         }];
//     } failure:^(NSString *msg) {
//         [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
//             [self.tblFavorites.tableFooterView setHidden:YES];
//         }];
//     }];
    
   
}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    
    UITableView *tableView = [self tableViewForMode:self.displayMode];
    [tableView flashScrollIndicators];
    [tableView deselectRowAtIndexPath:[tableView indexPathForSelectedRow] animated:animated];
    
    if (firstLoaded == YES)
    {
        [self setDisplayMode:_displayMode];
        
        // swipe
        CGRect frame = self.swipeView.frame;
        
        frame.origin.x = frame.size.width * [self indexForMode:_displayMode];
        frame.origin.y = 0;
        [self.swipeView setContentOffset:CGPointMake(frame.origin.x, 0)];
        
        self.transitioningData = NO;
        
        firstLoaded = NO;
    }
    else
        
    {
        [[self tableViewForMode:self.displayMode] reloadData];
    }
    
    // set pedometerviewdelegate
    AppDelegate *delegate = (AppDelegate *)[UIApplication sharedApplication].delegate;
    delegate.pedometerViewerDelegate = self;
    
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

- (void)setupSegmentControl
{
    float segment_width = 70.0;
    _segmentedControl = [[CustomSegmentedControl alloc] initWithItems:@[FOODS_TITLE, CURRENT_TITLE, FAVORITES_TITLE]];
    //_segmentedControl.tintAdjustmentMode = UIViewTintAdjustmentModeAutomatic;
    _segmentedControl.tintColor = [UIColor colorWithRed:244/255.0 green:204/255.0 blue:102/255.0 alpha:1.0];
    
    NSMutableDictionary *attributes = [@{
                                         NSForegroundColorAttributeName:[UIManager headerTextColor],
                                         NSFontAttributeName:[UIFont systemFontOfSize:11.0]
                                         } mutableCopy];
    [_segmentedControl setTitleTextAttributes:attributes forState:UIControlStateNormal];
    
    //attributes[NSUnderlineStyleAttributeName] = @(NSUnderlineStyleSingle);
    //attributes[NSTextEffectAttributeName] = NSTextEffectLetterpressStyle;
    
    /*
     NSShadow *shadow = [[NSShadow alloc] init];
     shadow.shadowColor = [UIColor colorWithWhite:0 alpha:1.0];
     shadow.shadowOffset = CGSizeMake(1.0, 1.0);
     shadow.shadowBlurRadius = 2.0;
     attributes[NSShadowAttributeName] = shadow;
     */
    
    
    /*
     attributes[NSBackgroundColorAttributeName] = [UIColor whiteColor];
     */
    attributes[NSForegroundColorAttributeName] = [UIColor whiteColor];
    
    
    [_segmentedControl setTitleTextAttributes:attributes forState:UIControlStateSelected];
    
    /*
     [_segmentedControl.layer setCornerRadius:4.0f];
     [_segmentedControl.layer setBorderColor:[UIColor colorWithRed:1.0 green:0.7 blue:0.14 alpha:1.0].CGColor];
     [_segmentedControl.layer setBorderWidth:1.5f];
     [_segmentedControl.layer setShadowColor:[UIColor blackColor].CGColor];
     [_segmentedControl.layer setShadowOpacity:0.8];
     [_segmentedControl.layer setShadowRadius:3.0];
     [_segmentedControl.layer setShadowOffset:CGSizeMake(2.0, 2.0)];
     */
    
    
    [_segmentedControl setSelectedSegmentIndex:0];
    [_segmentedControl addTarget:self action:@selector(segmentChanged:) forControlEvents:UIControlEventValueChanged];
    for(int i=0;i<_segmentedControl.numberOfSegments;i++){
        [_segmentedControl setWidth:segment_width forSegmentAtIndex:i];
    }
    
    [self.navigationItem setTitleView:_segmentedControl];
}

#pragma mark - navigation item actions

-(IBAction)addPressed:(id)sender {
    AddFoodViewController *vc = [self.storyboard instantiateViewControllerWithIdentifier:@"AddFoodViewController"];
    [self.navigationController pushViewController:vc animated:YES];
}

- (void)onBack:(id)sender {
    [self.navigationController popViewControllerAnimated:YES];
}

#pragma mark - Page Scrolling
- (void)scrollViewDidScroll:(UIScrollView *)scrollView
{

    if (scrollView == self.tblFoods)
    {
        if ((scrollView.contentOffset.y + scrollView.frame.size.height) >= scrollView.contentSize.height)
        {
            if (_searching)
            {
                if (_hasNextSearching)
                    self.tblFoods.tableFooterView.hidden = NO;
            }
            else
            {
                if (_hasNextNormal)
                    self.tblFoods.tableFooterView.hidden = NO;
            }
            // call method to add data to tableView
        }
        else
        {
            self.tblFoods.tableFooterView.hidden = YES;
        }
    }

    
}

/**
 * is called when a programmatic-generated scroll finishes.
 */
- (void)scrollViewDidEndScrollingAnimation:(UIScrollView *)scrollView
{
    if (scrollView != self.swipeView)
        return;
    
    UITableView *tableView = [self tableViewForMode:self.displayMode];
    [tableView flashScrollIndicators];
}

/**
 * called when  user-swipe scroll finishes.
 */
- (void)scrollViewDidEndDecelerating:(UIScrollView *)scrollView
{
    if (scrollView == self.swipeView)
    {
    
        CGFloat width = scrollView.frame.size.width;
        NSInteger page = (scrollView.contentOffset.x + (0.5f * width)) / width;
        
        
        self.segmentedControl.selectedSegmentIndex = page;
        [self.segmentedControl sendActionsForControlEvents:UIControlEventValueChanged];
        
        UITableView *tableView = [self tableViewForMode:self.displayMode];
        [tableView flashScrollIndicators];
    }
    else if (scrollView == self.tblFoods)
    {
        if (self.tblFoods.tableFooterView.hidden == NO)
        {
            if (_searching)
            {
                if (!_pendingSearching)
                {
                    _pendingSearching = YES;
                    
                    [Food getFoodsWithRemote:0 keyword:_keyword page:_nextPageSearching success:^(NSMutableArray *arrayData, BOOL hasNext)
                    {
                        [[NSOperationQueue mainQueue] addOperationWithBlock:^(){
                            _pendingSearching = NO;
                            _hasNextSearching = hasNext;
                            
                            [self.foodsSearchResults addObjectsFromArray:arrayData];
                            self.tblFoods.tableFooterView.hidden = YES;
                            [self.tblFoods reloadData];
                            
                            _nextPageSearching++;
                        }];
                        
                    }failure:^(NSString *msg) {
                        [[NSOperationQueue mainQueue] addOperationWithBlock:^(){
                            _pendingSearching = NO;
                            self.tblFoods.tableFooterView.hidden = YES;
                        }];
                    }];
                }
            }
            else
            {
                if (!_pendingNormal)
                {
                    _pendingNormal = YES;
                    
                    [Food getFoodsWithRemote:0 keyword:_keyword page:_nextPageNormal success:^(NSMutableArray *arrayData, BOOL hasNext)
                     {
                         [[NSOperationQueue mainQueue] addOperationWithBlock:^(){
                             _pendingNormal = NO;
                             _hasNextNormal = hasNext;
                             
                             [self.foods addObjectsFromArray:arrayData];
                             self.tblFoods.tableFooterView.hidden = YES;
                             [self.tblFoods reloadData];
                             
                             _nextPageNormal++;
                         }];
                         
                     }failure:^(NSString *msg) {
                         [[NSOperationQueue mainQueue] addOperationWithBlock:^(){
                             _pendingNormal = NO;
                             self.tblFoods.tableFooterView.hidden = YES;
                         }];
                     }];
                }
            }
        }

    }
}

#pragma mark - Segment control

- (int)indexForMode:(DisplayMode)mode
{
    switch (mode) {
        case DisplayModeFoods:
            return FOODS_SEGMENT_INDEX;
            break;
        case DisplayModeCurrent:
            return CURRENT_SEGMENT_INDEX;
            break;
        case DisplayModeFavorites:
            return FAVORITES_SEGMENT_INDEX;
            break;
            
        default:
            break;
    }
    return 0;
}

- (void)segmentChanged:(CustomSegmentedControl *)control
{
    //self.expIndexPath = nil;
    [self.refresh endRefreshing];
    
    NSLog(@"segmentChanged to index: %d", (int)control.selectedSegmentIndex);
    //_justLoaded = NO;
    if(control.selectedSegmentIndex == FOODS_SEGMENT_INDEX){
        if (_displayMode != DisplayModeFoods) {
            //Switch to Current
            //if(_userContact){
            //    self.currentContact = _userContact;
            //}
            self.displayMode = DisplayModeFoods;
        } else {
            [self scrollUp];
        }
    } else if(control.selectedSegmentIndex == CURRENT_SEGMENT_INDEX){
        if (_displayMode != DisplayModeCurrent) {
            //Switch to People
            self.displayMode = DisplayModeCurrent;
        } else {
            [self scrollUp];
        }
    } else if(control.selectedSegmentIndex == FAVORITES_SEGMENT_INDEX){
        if (_displayMode != DisplayModeFavorites) {
            //Switch to Hot Knotes
            self.displayMode = DisplayModeFavorites;
        } else {
            [self scrollUp];
        }
    }
}

- (void)setDisplayMode:(DisplayMode)displayMode {
    [self setDisplayMode:displayMode animated:YES];
}

- (void)setDisplayMode:(DisplayMode)displayMode animated:(BOOL)animated {
    DisplayMode oldDisplayMode = _displayMode;
    int pageIndex = 0;
    
    NSLog(@"setDisplayMode from %d to %d animated? %d", oldDisplayMode, displayMode, animated);
    switch (displayMode) {
        case DisplayModeFoods:
            

            self.navigationItem.title = FOODS_TITLE;
            //self.navigationItem.rightBarButtonItem = _addButton;
            self.navigationItem.rightBarButtonItem = nil;
            
            
            pageIndex = FOODS_SEGMENT_INDEX;
            
            break;
        case DisplayModeCurrent:
            

            self.navigationItem.title = CURRENT_TITLE;
            self.navigationItem.rightBarButtonItem = nil;
            

            pageIndex = CURRENT_SEGMENT_INDEX;
            
            
            
            break;
        case DisplayModeFavorites:
            



            self.navigationItem.title = FAVORITES_TITLE;
            self.navigationItem.rightBarButtonItem = nil;
            
            pageIndex = FAVORITES_SEGMENT_INDEX;

            break;
    }
    
    
    _displayMode = displayMode;
    
    if(_segmentedControl.selectedSegmentIndex != displayMode){
        [_segmentedControl setSelectedSegmentIndex:displayMode];
    }
    
    // must consider the following part
    /*
     if (displayMode == DisplayModePeople && self.searchingPeople) {
     //[self.searchBar resignFirstResponder];
     [self.searchBar becomeFirstResponder];
     self.searchBar.text = self.searchString;
     self.searchingPeople = NO;
     }
     */
    
    
    self.transitioningData = YES;
    self.oldDisplayMode = oldDisplayMode;
    
    
    [self updateViewConstraints];
    
    //[self.tableView setContentOffset:CGPointZero animated:YES];
    
    // swipe
    CGRect frame = self.swipeView.frame;
    
    frame.origin.x = frame.size.width * pageIndex;
    frame.origin.y = 0;
    [self.swipeView scrollRectToVisible:frame animated:animated];
    
    UITableView *tableView = [self tableViewForMode:_displayMode];
    [tableView reloadData];
    
    self.transitioningData = NO;
    
    
    
}

- (void)updateViewConstraints {
    [super updateViewConstraints];
    
    // constraints
    
}


- (void)scrollUp
{
    UITableView *tableView = [self tableViewForMode:self.displayMode];
    
    [tableView scrollToRowAtIndexPath:[NSIndexPath indexPathForRow:NSNotFound inSection:0] atScrollPosition:UITableViewScrollPositionTop animated:YES];
    
}

- (UITableView *)tableViewForMode:(DisplayMode)mode
{
    switch (self.displayMode) {
        case DisplayModeFoods:
            
            
            return self.tblFoods;
            
            break;
        case DisplayModeCurrent:
            
            
            return self.tblCurrents;
            
            break;
        case DisplayModeFavorites:
            
            return self.tblFavorites;
            
            break;
    }
    return nil;
}

#pragma mark - progress view
- (void)refreshProgress
{
    // get params from context
    AppContext *context = [AppContext sharedContext];
    _totalCalories = 0;
    for (Food *food in [User currentUser].currentFoods) {
        _totalCalories += food.calories;
    }
    //_totalCalories = context.totalCalories;
    _caloriesBurned = context.totalCalories - context.caloriesToBurn;
    if (_caloriesBurned < 0)
        _caloriesBurned = 0;
    
    CGRect frame = self.progress_back.frame;
    if (_totalCalories == 0)
        frame.size.width = 0;
    else
        frame.size.width = frame.size.width * _caloriesBurned / _totalCalories;
    
    self.progressbar.frame = frame;
    
    self.lblCalories.text = [NSString stringWithFormat:@"%d/%d Calories", (int)_caloriesBurned, (int)_totalCalories];
}

#pragma mark - Table view data source

- (NSMutableArray *)dataForTable:(UITableView *)tableView
{
    DisplayMode mode = _displayMode;
    if(self.transitioningData){
        mode = _oldDisplayMode;
    }
    if (tableView == self.tblFoods)
    {
        if (_searching)
            return self.foodsSearchResults;
        return self.foods;
    }
    else if (tableView == self.tblCurrents)
        return [self dataForCurrentFoods];
    else if (tableView == self.tblFavorites)
        return [self dataForFavoritesFoods];
    //else if (tableView == _searchController.searchResultsTableView)
      //  return self.foodsSearchResults;
    return [[NSMutableArray alloc] init];
}


- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView {
    return 1;
}


- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    NSLog(@"numberOfRowsInSection %d: %d", (int)section, (int)[self dataForTable:tableView].count);
    if(self.transitioningData){
        self.transitioningData = NO;
        NSLog(@"Done transitioningData");
    }
    
    int offset = 0;
    /*
     if (_displayMode == DisplayModeFoods) {
     //offset = 1;
     //NSLog(@"offset 1 for new  knote");
     } else if (_displayMode == DisplayModeSpaces && _topicArray.count == 0) {
     offset = 1;
     NSLog(@"offset 1 for create a pad button");
     }
     */
    return [self dataForTable:tableView].count + offset;
}

- (CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    DisplayMode mode = _displayMode;
    if(self.transitioningData){
        mode = _oldDisplayMode;
    }
    
    CGFloat height = 0;
    
    switch (mode){
        case DisplayModeFoods:
            height = [self tableView:tableView foodsCellHeightForRowAtIndexPath:indexPath];
            break;
        case DisplayModeCurrent:
            height = [self tableView:tableView currentCellHeightForRowAtIndexPath:indexPath];
            break;
        case DisplayModeFavorites:
            height = [self tableView:tableView favoritesCellHeightForRowAtIndexPath:indexPath];
            break;
    }
    return height;
}

static FoodTableViewCell *_prototypeFoodCell = nil;
- (FoodTableViewCell *)prototypeFoodCell
{
    if (_prototypeFoodCell == nil)
        _prototypeFoodCell = [self.tblFoods dequeueReusableCellWithIdentifier:@"cellidentifier"];
    return _prototypeFoodCell;
}


- (CGFloat)tableView:(UITableView *)tableView foodsCellHeightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    Food *food = (Food *)[[self dataForTable:tableView] objectAtIndex:indexPath.row];
    float height = [[self prototypeFoodCell] heightForFood:food];
    return height;
}

- (CGFloat)tableView:(UITableView *)tableView currentCellHeightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    Food *food = (Food *)[[self dataForTable:tableView] objectAtIndex:indexPath.row];
    float height = [[self prototypeFoodCell] heightForFood:food];
    return height;
}

- (CGFloat)tableView:(UITableView *)tableView favoritesCellHeightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    Food *food = (Food *)[[self dataForTable:tableView] objectAtIndex:indexPath.row];
    float height = [[self prototypeFoodCell] heightForFood:food];
    return height;
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    DisplayMode mode = _displayMode;
    if(self.transitioningData){
        mode = _oldDisplayMode;
    }
    UITableViewCell *cell = nil;
    
    switch (mode){
        case DisplayModeFoods:
            cell = [self tableView:tableView foodsCellForRowAtIndexPath:indexPath];
            break;
        case DisplayModeCurrent:
            cell = [self tableView:tableView currentCellForRowAtIndexPath:indexPath];
            break;
        case DisplayModeFavorites:
            cell = [self tableView:tableView favoritesCellForRowAtIndexPath:indexPath];
            break;
            default:
            cell = nil;
    }
    return cell;
}

- (UITableViewCell *)tableView:(UITableView *)tableView foodsCellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    FoodTableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:@"cellidentifier"];
    Food *food = (Food *)[[self dataForTable:tableView] objectAtIndex:indexPath.row];
    [cell bind:food];
    cell.delegate = self;
    return cell;
}

- (UITableViewCell *)tableView:(UITableView *)tableView currentCellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    CurrentTableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:@"cellidentifier"];
    Food *food = (Food *)[[self dataForTable:tableView] objectAtIndex:indexPath.row];
    CurrentFood *currentFood = (CurrentFood *)food;
    [cell bind:food];
    cell.delegate = self;
    return cell;
}

- (UITableViewCell *)tableView:(UITableView *)tableView favoritesCellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    FavoritesTableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:@"cellidentifier"];
    Food *food = (Food *)[[self dataForTable:tableView] objectAtIndex:indexPath.row];
    [cell bind:food];
    cell.delegate = self;
    return cell;
}

#pragma mark - table view delegate

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    
     //self.expIndexPath = nil;
     switch (_displayMode){
         case DisplayModeFoods:
             [self openFoodRowInTableView:tableView atIndexPath:indexPath];
             break;
     case DisplayModeCurrent:
             [self openCurrentRowInTableView:tableView atIndexPath:indexPath];
             break;
     case DisplayModeFavorites:
             [self openFavoritesRowInTableView:tableView atIndexPath:indexPath];
             break;
     }
     
}

- (void)openFoodRowInTableView:(UITableView *)tableView atIndexPath:(NSIndexPath *)indexPath
{
    Food *food = [[self dataForTable:tableView] objectAtIndex:indexPath.row];
    
    FoodInfoViewController *vc = [self.storyboard instantiateViewControllerWithIdentifier:@"FoodInfoViewController"];
    vc.food = food;
    [self.navigationController pushViewController:vc animated:YES];
}

- (void)openCurrentRowInTableView:(UITableView *)tableView atIndexPath:(NSIndexPath *)indexPath
{
    Food *food = [[self dataForTable:tableView] objectAtIndex:indexPath.row];
    
    FoodInfoViewController *vc = [self.storyboard instantiateViewControllerWithIdentifier:@"FoodInfoViewController"];
    vc.food = food;
    [self.navigationController pushViewController:vc animated:YES];
}

- (void)openFavoritesRowInTableView:(UITableView *)tableView atIndexPath:(NSIndexPath *)indexPath
{
    Food *food = [[self dataForTable:tableView] objectAtIndex:indexPath.row];
    
    FoodInfoViewController *vc = [self.storyboard instantiateViewControllerWithIdentifier:@"FoodInfoViewController"];
    vc.food = food;
    [self.navigationController pushViewController:vc animated:YES];
}

#if false
- (UITableViewCellEditingStyle) tableView:(UITableView *)tableView editingStyleForRowAtIndexPath:(NSIndexPath *)indexPath
{
    return UITableViewCellEditingStyleNone;
}
// Override to support editing the table view.
- (void)tableView:(UITableView *)tableView commitEditingStyle:(UITableViewCellEditingStyle)editingStyle forRowAtIndexPath:(NSIndexPath *)indexPath
{
    /*
     if (editingStyle == UITableViewCellEditingStyleDelete) {
     NSLog(@"commitEditingStyle Delete");
     // Delete the row from the data source
     [[self dataForTable:tableView] removeObjectAtIndex:indexPath.row];
     if (tableView == _tableView && _displayMode == DisplayModeSpaces && _topicArray.count == 0) {
     [self.tableView reloadData];
     } else {
     [tableView deleteRowsAtIndexPaths:@[indexPath] withRowAnimation:UITableViewRowAnimationFade];
     [self.tableView performSelector:@selector(reloadData) withObject:nil afterDelay:0];
     }
     }
     else if (editingStyle == UITableViewCellEditingStyleInsert) {
     // Create a new instance of the appropriate class, insert it into the array, and add a new row to the table view
     }
     */
}
// Override to support conditional editing of the table view.
- (BOOL)tableView:(UITableView *)tableView canEditRowAtIndexPath:(NSIndexPath *)indexPath
{
    /*
     // Return NO if you do not want the specified item to be editable.
     //    if(tableView == _searchController.searchResultsTableView){
     //        return NO;
     //    }
     
     if (_displayMode == DisplayModeHot) {
     return NO;
     }
     
     return YES;
     */
    return YES;
}
#endif
- (IBAction)keywordChanged:(id)sender
{
    if (self.txtKeyword.text.length <= 0)
    {
        if (_searching)
        {
            // cancel search
            self.btnSearchCancel.hidden = YES;
            
            [self.foodsSearchResults removeAllObjects];
            _searching = NO;
            [self.tblFoods reloadData];
        }
    }
    else
    {
        _searching = YES;
        self.btnSearchCancel.hidden = NO;
        _keyword = self.txtKeyword.text;
        _nextPageSearching = 0;
        _pendingSearching = YES;
        __block NSMutableArray *arrayLocalFoods = nil;
        [Food getFoodsWithLocal:[User currentUser].uid keyword:_keyword page:0 success:^(NSMutableArray *foods) {
            arrayLocalFoods = foods;
        } failure:^(NSString *msg) {
            //
        }];
        
        
        [Food getFoodsWithRemote:0 keyword:_keyword page:0 success:^(NSMutableArray *arrayData, BOOL hasNext) {
            [[NSOperationQueue mainQueue] addOperationWithBlock:^{
                _nextPageSearching = 1;
                self.foodsSearchResults = arrayLocalFoods;
                [self.foodsSearchResults addObjectsFromArray:arrayData];
                _hasNextSearching = hasNext;
                
                [self.tblFoods reloadData];
                _pendingSearching = NO;
            }];
            
        } failure:^(NSString *msg) {
            [[NSOperationQueue mainQueue] addOperationWithBlock:^{
                _nextPageSearching = 0;
                [self.foodsSearchResults removeAllObjects];
                [self.tblFoods reloadData];
                _pendingSearching = NO;
            }];
            
        }];
    }
}

- (IBAction)onCancelSearching:(id)sender
{
    _keyword = @"";
    self.txtKeyword.text = @"";
    self.btnSearchCancel.hidden = YES;
    _searching = NO;
    [self.tblFoods reloadData];
}

#pragma mark - UISearchDisplayDelegate methods

#if false

- (void)updateSearchResultsForString:(NSString *)searchString
{
    //self.searchString = searchString;
    NSMutableArray *searchResults = nil;
    switch (_displayMode) {
        case DisplayModeFoods:
            searchResults = _foodsSearchResults;
            break;
            
        default:
            break;
    }
    
    [searchResults removeAllObjects];
    
    NSUInteger searchOptions = NSCaseInsensitiveSearch | NSDiacriticInsensitiveSearch;
    
    /*
     if(_displayMode == DisplayModePeople){
     for (ContactsEntity *contact in _peopleData) {
     
     NSString *searchAgainst = contact.name;
     if(!searchAgainst || searchAgainst.length == 0){
     continue;
     }
     NSRange foundRange = [searchAgainst rangeOfString:searchString options:searchOptions range:NSMakeRange(0, searchAgainst.length)];
     if (foundRange.length > 0){
     [searchResults addObject:contact];
     }
     }
     } else if(_displayMode == DisplayModeSpaces){
     for (TopicInfo *tInfo in _topicArray) {
     NSString *searchAgainst = tInfo.entity.topic;
     if(!searchAgainst || searchAgainst.length == 0){
     continue;
     }
     NSRange foundRange = [searchAgainst rangeOfString:searchString options:searchOptions range:NSMakeRange(0, searchAgainst.length)];
     if (foundRange.length > 0){
     [searchResults addObject:tInfo];
     }
     }
     }
     */
}


- (BOOL)searchDisplayController:(UISearchDisplayController *)controller shouldReloadTableForSearchString:(NSString *)searchString
{
    [self updateSearchResultsForString:searchString];
    return YES;
}


- (void)searchDisplayController:(UISearchDisplayController *)controller didLoadSearchResultsTableView:(UITableView *)tableView
{
    NSLog(@"didLoadSearchResultsTableView");
    tableView.backgroundColor = [UIColor clearColor];
    //tableView.backgroundView = [DesignManager appBackgroundView];
    tableView.backgroundColor = [UIManager appBackgroundColor];
    
    tableView.separatorColor = [UIColor colorWithWhite:0.77 alpha:1.0];
    
#if __IPHONE_OS_VERSION_MAX_ALLOWED >= 70000
    if (IOS7_OR_LATER) {
        [tableView setSeparatorInset:UIEdgeInsetsZero];//
    }
#endif
    //Hide empty separators
    UIView *v = [[UIView alloc] initWithFrame:CGRectZero];
    v.backgroundColor = [UIColor clearColor];
    tableView.tableFooterView = v;
}
- (void)searchDisplayController:(UISearchDisplayController *)controller willShowSearchResultsTableView:(UITableView *)tableView
{
    //_searchMode = YES;
}
- (void)searchDisplayController:(UISearchDisplayController *)controller willHideSearchResultsTableView:(UITableView *)tableView
{
    //_searchMode = NO;
}
- (void)searchTableViewSwiped:(UISwipeGestureRecognizer *)gestureRecognizer {
    NSIndexPath * indexPath = [self cellIndexPathForGestureRecognizer:gestureRecognizer];
    if(indexPath == nil)
        return;
    
    if(![_searchController.searchResultsTableView.dataSource tableView:_searchController.searchResultsTableView canEditRowAtIndexPath:indexPath]) {
        return;
    }
    /*
     if(gestureRecognizer == _rightGestureRecognizer && ![_editingIndexPath isEqual:indexPath]) {
     UITableViewCell * cell = [_searchController.searchResultsTableView cellForRowAtIndexPath:indexPath];
     [self setEditing:YES atPrivateIndexPath:indexPath cell:cell];
     } else if (gestureRecognizer == _leftGestureRecognizer && [_editingIndexPath isEqual:indexPath]){
     UITableViewCell * cell = [_searchController.searchResultsTableView cellForRowAtIndexPath:indexPath];
     [self setEditing:NO atPrivateIndexPath:indexPath cell:cell];
     }
     */
}

- (void)searchTableViewTaped:(UIGestureRecognizer *)gestureRecognizer
{
    /*
     if(_editingIndexPath) {
     UITableViewCell * cell = [_searchController.searchResultsTableView cellForRowAtIndexPath:_editingIndexPath];
     [self setEditing:NO atPrivateIndexPath:_editingIndexPath cell:cell];
     }
     */
}

- (NSIndexPath *)cellIndexPathForGestureRecognizer:(UIGestureRecognizer *)gestureRecognizer {
    UIView * view = gestureRecognizer.view;
    if(![view isKindOfClass:[UITableView class]]) {
        return nil;
    }
    
    CGPoint point = [gestureRecognizer locationInView:view];
    NSIndexPath * indexPath = [_searchController.searchResultsTableView indexPathForRowAtPoint:point];
    return indexPath;
}

- (void)setEditing:(BOOL)editing atPrivateIndexPath:indexPath cell:(UITableViewCell *)cell {
    /*
     if(editing) {
     if(_editingIndexPath) {
     UITableViewCell * editingCell = [_searchController.searchResultsTableView cellForRowAtIndexPath:_editingIndexPath];
     [self setEditing:NO atIndexPath:_editingIndexPath cell:editingCell];
     }
     [_searchController.searchResultsTableView addGestureRecognizer:_tapGestureRecognizer];
     } else {
     [_searchController.searchResultsTableView removeGestureRecognizer:_tapGestureRecognizer];
     }
     
     if(editing) {
     _editingIndexPath = indexPath;
     } else {
     _editingIndexPath = nil;
     }
     
     if ([self respondsToSelector:@selector(setEditing:atIndexPath:cell:)]) {
     [self setEditing:editing atIndexPath:indexPath cell:cell];
     }
     */
}
#endif

- (void)viewDidAppear:(BOOL)animated
{
    [super viewDidAppear:animated];
    
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(keyboardShowing:)
                                                 name:UIKeyboardWillShowNotification
                                               object:nil];
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(keyboardHiding:)
                                                 name:UIKeyboardWillHideNotification
                                               object:nil];
    
    
    [self refreshProgress];
}
- (void)viewWillDisappear:(BOOL)animated
{
    [super viewWillDisappear:animated];
    
    [[NSNotificationCenter defaultCenter] removeObserver:self name:UIKeyboardWillShowNotification object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:UIKeyboardWillHideNotification object:nil];
    
    // set delegate to nil;
    AppDelegate *delegate = (AppDelegate *)[UIApplication sharedApplication].delegate;
    delegate.pedometerViewerDelegate = nil;
}

#pragma mark -
#pragma mark UITextFieldDelegate Methods

-(BOOL)textFieldShouldReturn:(UITextField *)textField {
	[textField resignFirstResponder];
	return YES;
}

- (void)textFieldDidBeginEditing:(UITextField *)textField {
    currentResponder = textField;
}

- (void)textFieldDidEndEditing:(UITextField *)textField {
    currentResponder = nil;
}

#pragma mark Keyboard Methods

- (void)keyboardShowing:(NSNotification *)note
{
    NSNumber *duration = note.userInfo[UIKeyboardAnimationDurationUserInfoKey];
    //CGRect endFrame = ((NSValue *)note.userInfo[UIKeyboardFrameEndUserInfoKey]).CGRectValue;
    //    _loginGroupTopConstraint.with.offset(60.0);
    //
    //
    //    [UIView animateWithDuration:duration.floatValue animations:^{
    //        self.logo.alpha = 0.0;
    //        [self.view layoutIfNeeded];
    //    }];
}

- (void)keyboardHiding:(NSNotification *)note
{
    NSNumber *duration = note.userInfo[UIKeyboardAnimationDurationUserInfoKey];
    
    //    _loginGroupTopConstraint.with.offset(loginState == LoginStateLoggingIn ? textFieldsLowerPos : textFieldsUpperPos);
    //
    //    [UIView animateWithDuration:duration.floatValue animations:^{
    //        self.logo.alpha = 1.0;
    //        [self.view layoutIfNeeded];
    //    }];
    
}

# pragma mark Gesture selector
- (void)backgroundTap:(UITapGestureRecognizer *)backgroundTap {
    if(currentResponder){
        [currentResponder resignFirstResponder];
    }
}

- (BOOL)gestureRecognizer:(UIGestureRecognizer *)gestureRecognizer shouldReceiveTouch:(UITouch *)touch
{
    if ([touch.view isDescendantOfView:[self tableViewForMode:self.displayMode]]) {
        
        // Don't let selections of auto-complete entries fire the
        // gesture recognizer
        if (currentResponder)
            return YES;
        return NO;
    }
    
    return YES;
}

#pragma mark - Foods View Cell delegate

// plus button on Food
// add food direct to current foods/meals (total calories)
- (void)onFoodCellBtnPlus:(Food *)food
{
    SHOW_PROGRESS(@"Please Wait");
    [CurrentFood addFoodToCurrentWithLocal:[User currentUser].uid food:food success:^() {
        [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
            //[AutoMessageBox AutoMsgInView:self withText:@"Success" withSuccess:YES];
            HIDE_PROGRESS_WITH_SUCCESS(@"Success");
            //[[self dataForCurrentFoods] addObject:food];
            [self refreshCurrentFoods];
            
            [self.tblCurrents reloadData];
            [self refreshProgress];
            
        }];
    } failure:^(NSString *msg) {
        [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
            // alert msg
            //[AutoMessageBox AutoMsgInView:self withText:@"Failure" withSuccess:NO];
            HIDE_PROGRESS_WITH_FAILURE(([NSString stringWithFormat:@"Failure : %@", msg]));
        }];
    }];
}

// minus button on Current
// remove food from current foods/meals
- (void)onCurrentCellBtnMinus:(Food *)food
{
    CurrentFood *currentFood = (CurrentFood *)food;
    SHOW_PROGRESS(@"Please Wait");
    [CurrentFood removeFoodFromCurrentWithLocal:[User currentUser].uid currentFood:currentFood success:^() {
        [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
            
            //[AutoMessageBox AutoMsgInView:self withText:@"Success" withSuccess:YES];
            HIDE_PROGRESS_WITH_SUCCESS(@"Success");
            
            //[[self dataForCurrentFoods] removeObject:food];
            [self refreshCurrentFoods];
            
            [self.tblCurrents reloadData];
            [self refreshProgress];
            
        }];
    } failure:^(NSString *msg) {
        [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
            // alert msg
            //[AutoMessageBox AutoMsgInView:self withText:@"Failure" withSuccess:NO];
            HIDE_PROGRESS_WITH_FAILURE(([NSString stringWithFormat:@"Failure : %@", msg]));
        }];
    }];
}

// add food direct to current foods/meals (total calories)
- (void)onFavoritesCellBtnPlus:(Food *)food
{
    SHOW_PROGRESS(@"Please Wait");
    [CurrentFood addFoodToCurrentWithLocal:[User currentUser].uid food:food success:^() {
        [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
            
            //[AutoMessageBox AutoMsgInView:self withText:@"Success" withSuccess:YES];
            HIDE_PROGRESS_WITH_SUCCESS(@"Success");
            
            //[[self dataForCurrentFoods] addObject:food];
            [self refreshCurrentFoods];
            
            [self.tblCurrents reloadData];
            [self refreshProgress];
            
        }];
    } failure:^(NSString *msg) {
        [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
            // alert msg
            //[AutoMessageBox AutoMsgInView:self withText:@"Success" withSuccess:NO];
            HIDE_PROGRESS_WITH_FAILURE(([NSString stringWithFormat:@"Failure : %@", msg]));
        }];
    }];
}

// remove food from favorites foods/meals
- (void)onFavoritesCellBtnMinus:(Food *)food
{
    SHOW_PROGRESS(@"Please Wait");
    [Food removeFoodFromFavoritesWithLocal:[User currentUser].uid food:food success:^() {
        [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
            
            //[AutoMessageBox AutoMsgInView:self withText:@"Success" withSuccess:YES];
            HIDE_PROGRESS_WITH_SUCCESS(@"Success");
            
            [[self dataForFavoritesFoods] removeObject:food];
            
            [self.tblFavorites reloadData];
            
        }];
    } failure:^(NSString *msg) {
        [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
            // alert msg
            //[AutoMessageBox AutoMsgInView:self withText:@"Success" withSuccess:NO];
            HIDE_PROGRESS_WITH_FAILURE(([NSString stringWithFormat:@"Failure : %@", msg]));
        }];
    }];
}

#pragma mark data source
- (NSMutableArray *)dataForCurrentFoods
{
    return [User currentUser].currentFoods;
}

- (NSMutableArray *)dataForFavoritesFoods
{
    return [User currentUser].favoritesFoods;
}

- (void)setDataForCurrentFoods:(NSMutableArray *)arrayData
{
    [User currentUser].currentFoods = [[NSMutableArray alloc] initWithArray:arrayData];
}

- (void)setDataForFavoritesFoods:(NSMutableArray *)arrayData
{
    [User currentUser].favoritesFoods = [[NSMutableArray alloc] initWithArray:arrayData];
}

- (void)refreshCurrentFoods
{
    [CurrentFood getCurrentFoodsWithLocal:[User currentUser].uid isConsumed:NO success:^(NSMutableArray *foods) {
        [self setDataForCurrentFoods:foods];
    } failure:^(NSString *msg) {
        //
    }];
}

#pragma mark PedometerViewDelegate
- (void)updateNumberOfSteps:(NSInteger)numberOfSteps
{
    [self refreshProgress];
}

- (void)consumedCurrentFoods:(NSInteger)stepsTaken withDate:(NSDate *)date
{
    [self refreshProgress];
}


@end
