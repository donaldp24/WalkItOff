//
//  CombinedViewController.m
//  WalkItOff
//
//  Created by Donald Pae on 6/13/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "CombinedViewController.h"
#import "CustomSegmentedControl.h"
#import "SwipeTableView.h"
#import "UIManager.h"
#import "FoodTableViewCell.h"

#import "Food.h"

static NSString *FoodsCellIdentifier = @"FoodsCell";
static NSString *CurrentCellIdentifier = @"CurrentCell";
static NSString *FavoritesCellIdentifier = @"FavoritesCell";

static NSString *FOODS_TITLE = @"Library";
static NSString *CURRENT_TITLE = @"Currents";
static NSString *FAVORITES_TITLE = @"Favorites";


static NSUInteger FOODS_SEGMENT_INDEX = 0;
static NSUInteger CURRENT_SEGMENT_INDEX = 1;
static NSUInteger FAVORITES_SEGMENT_INDEX = 2;



@interface CombinedViewController () {
    UIBarButtonItem *_addButton;
    UIBarButtonItem *_backButton;
    
    UISwipeGestureRecognizer * _leftGestureRecognizer;
    UISwipeGestureRecognizer * _rightGestureRecognizer;
    UITapGestureRecognizer * _tapGestureRecognizer;
    BOOL _searchMode;
}

@property(nonatomic,retain) SwipeTableView *tableView;
@property (nonatomic, strong) UISwipeGestureRecognizer* swipeLeftRecognizer;
@property (nonatomic, strong) UISwipeGestureRecognizer* swipeRightRecognizer;
@property (nonatomic, strong) CustomSegmentedControl* segmentedControl;
@property (nonatomic, assign) DisplayMode displayMode;
@property (nonatomic, assign) DisplayMode oldDisplayMode;
@property (nonatomic, assign) BOOL transitioningData;

@property (nonatomic, strong) UILabel *lblTitle;

@property (nonatomic, strong) UISearchDisplayController *searchController;
@property (nonatomic, strong) UISearchBar *searchBar;

@property (nonatomic, strong) NSMutableArray *foods;
@property (nonatomic, strong) NSMutableArray *currentFoods;
@property (nonatomic, strong) NSMutableArray *favoritesFoods;

@property (nonatomic, strong) NSMutableArray *foodsSearchResults;
@property (nonatomic, strong) NSMutableArray *currentFoodsSearchResults;
@property (nonatomic, strong) NSMutableArray *favoritesFoodsSearchResults;

@property (nonatomic, strong) UIView *viewStatus;
@property (nonatomic, strong) UIImageView *progressBack;
@property (nonatomic, strong) UIImageView *progressBar;
@property (nonatomic, strong) UILabel *lblCalories;

@end

@implementation CombinedViewController

- (id)initWithNibName:(NSString *)nibNameOrNil bundle:(NSBundle *)nibBundleOrNil
{
    self = [super initWithNibName:nibNameOrNil bundle:nibBundleOrNil];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)reloadData
{
    [self.tableView reloadData];
    [self.tableView flashScrollIndicators];
}

- (void)initDisplayMode:(DisplayMode) mode
{
    _displayMode = mode;
}
- (void)initTempData
{
    Food *food;
    
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Piza";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Food";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Food";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Food";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Food";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Food";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Food";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Food";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Food";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Food";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Food";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Food";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Food";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Food";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Food";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Food";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    // Do any additional setup after loading the view.
    
    // init models
    self.foods = [[NSMutableArray alloc] init];
    self.currentFoods = [[NSMutableArray alloc] init];
    self.favoritesFoods = [[NSMutableArray alloc] init];
    
    self.foodsSearchResults = [[NSMutableArray alloc] init];
    self.currentFoodsSearchResults = [[NSMutableArray alloc] init];
    self.favoritesFoodsSearchResults = [[NSMutableArray alloc] init];
    
    [self initTempData];
    /////
    
    self.navigationItem.hidesBackButton = YES;
    self.navigationItem.title = FOODS_TITLE;
    
    // add button
    _addButton = [[UIBarButtonItem alloc] initWithBarButtonSystemItem:UIBarButtonSystemItemAdd target:self action:@selector(addPressed)];
    self.navigationItem.rightBarButtonItem = _addButton;
    
    // back button
    
    _backButton = [[UIBarButtonItem alloc] initWithImage:[UIImage imageNamed:@"backicon"] style:UIBarButtonItemStylePlain target:self action:@selector(onBack:)];
    self.navigationItem.leftBarButtonItem = _backButton;
    
    
    if ([self respondsToSelector:@selector(setAutomaticallyAdjustsScrollViewInsets:)])
        self.automaticallyAdjustsScrollViewInsets = YES;
    
    
    
    
    CGRect frame = self.view.bounds;
    self.tableView = [[SwipeTableView alloc] initWithFrame:frame];
    self.tableView.swipeDelegate = self;
    self.tableView.delegate = self;
    self.tableView.dataSource = self;
    
    UIView *rootView = [[UIView alloc] initWithFrame:CGRectZero];
    [rootView addSubview:self.tableView];
    rootView.backgroundColor = [UIColor whiteColor];
    self.view = rootView;
    
    

    _swipeLeftRecognizer = [[UISwipeGestureRecognizer alloc] initWithTarget:self action:@selector(panRight:)];
    _swipeLeftRecognizer.direction = UISwipeGestureRecognizerDirectionLeft;
    _swipeLeftRecognizer.enabled = YES;
    [self.tableView addGestureRecognizer:_swipeLeftRecognizer];
    
    _swipeRightRecognizer = [[UISwipeGestureRecognizer alloc] initWithTarget:self action:@selector(swipedRight:)];
    _swipeRightRecognizer.direction = UISwipeGestureRecognizerDirectionRight;
    _swipeRightRecognizer.enabled = YES;
    [self.tableView addGestureRecognizer:_swipeRightRecognizer];
    
    
    UIScreenEdgePanGestureRecognizer *screenEdgeLeftRecognizer = [[UIScreenEdgePanGestureRecognizer alloc] initWithTarget:self action:@selector(screenEdgeSwipedLeft:)];
    screenEdgeLeftRecognizer.edges = UIRectEdgeLeft;
    screenEdgeLeftRecognizer.enabled = YES;
    [self.view addGestureRecognizer:screenEdgeLeftRecognizer];
    
    UIScreenEdgePanGestureRecognizer *screenEdgeRightRecognizer = [[UIScreenEdgePanGestureRecognizer alloc] initWithTarget:self action:@selector(screenEdgeSwipedRight:)];
    screenEdgeRightRecognizer.edges = UIRectEdgeRight;
    screenEdgeRightRecognizer.enabled = YES;
    [self.view addGestureRecognizer:screenEdgeRightRecognizer];
    
    [self setupSegmentControl];
    
    self.viewStatus = [UIView new];
    self.progressBack = [[UIImageView alloc] initWithImage:[UIImage imageNamed:@"progress_back"]];
    self.progressBar = [[UIImageView alloc] initWithImage:[UIImage imageNamed:@"progressbar"]];
    self.lblCalories = [[UILabel alloc] init];
    self.lblCalories.text = @"800/1000 Calories Burned";
    self.lblCalories.textColor = [UIColor colorWithRed:73/255.0 green:73/255.0 blue:73/255.0 alpha:1.0];
    
    [self.viewStatus addSubview:self.progressBack];
    [self.viewStatus addSubview:self.progressBar];
    [self.viewStatus addSubview:self.lblCalories];
    [self.view addSubview:self.viewStatus];
    self.viewStatus.hidden = YES;
    
    // title view
    UIView *title = [[UIView alloc] init];
    [title setBackgroundColor:[UIManager appBackgroundColor]];
    self.lblTitle = [[UILabel alloc] init];
    [self.lblTitle setBackgroundColor:[UIColor clearColor]];
    self.lblTitle.text = @"";
    [self.lblTitle setTextColor:[UIColor colorWithRed:38/255.0 green:38/255.0 blue:38/255.0 alpha:1.0]];
    [self.lblTitle setFont:[UIFont systemFontOfSize:20]];
    [self.lblTitle setTextAlignment:NSTextAlignmentCenter];
    [title addSubview:self.lblTitle];
    [self.view addSubview:title];
    
    [title mas_makeConstraints:^(MASConstraintMaker *make) {
        make.left.equalTo(self.view);
        make.top.equalTo(self.view);
        make.right.equalTo(self.view);
        make.height.equalTo(@(100));
    }];
    
    [self.lblTitle mas_makeConstraints:^(MASConstraintMaker *make) {
        make.left.equalTo(title);
        make.right.equalTo(title);
        make.height.equalTo(@(30));
        make.bottom.equalTo(title);
    }];
    
    //self.tableView.translatesAutoresizingMaskIntoConstraints = YES;
    [self.tableView mas_makeConstraints:^(MASConstraintMaker *make) {
        //make.edges.equalTo(@0.0);
        
        make.top.equalTo(title.mas_bottom);
        make.left.equalTo(self.view);
        make.right.equalTo(self.view);
        make.bottom.equalTo(self.view);
        
    }];
    
    
    
    _searchBar = [[UISearchBar alloc] initWithFrame:CGRectMake(0, 0, self.view.bounds.size.width, 44.0)];
    if ([_searchBar respondsToSelector:@selector(setSearchBarStyle:)])
        _searchBar.searchBarStyle = UISearchBarStyleDefault;
    //_searchBar.tintColor = [UIColor whiteColor];
    //_searchBar.backgroundColor = [UIColor clearColor];
    
    
    
    
    //[self.tableView setContentOffset:CGPointMake(0, 100) animated:YES];

    //[self.tableView setTableHeaderView:_searchBar];
    //[self.tableView setContentOffset:CGPointMake(0, _searchBar.frame.size.height) animated:NO];
    
    
    _searchController = [[UISearchDisplayController alloc] initWithSearchBar:_searchBar contentsController:self];
    _searchController.delegate = self;
    _searchController.searchResultsDelegate = self;
    _searchController.searchResultsDataSource = self;
    
    
    _leftGestureRecognizer = [[UISwipeGestureRecognizer alloc] initWithTarget:self action:@selector(searchTableViewSwiped:)];
    _leftGestureRecognizer.direction = UISwipeGestureRecognizerDirectionLeft;
    _leftGestureRecognizer.delegate = self;
    [_searchController.searchResultsTableView addGestureRecognizer:_leftGestureRecognizer];
    
    _rightGestureRecognizer = [[UISwipeGestureRecognizer alloc] initWithTarget:self action:@selector(searchTableViewSwiped:)];
    _rightGestureRecognizer.delegate = self;
    _rightGestureRecognizer.direction = UISwipeGestureRecognizerDirectionRight;
    [_searchController.searchResultsTableView addGestureRecognizer:_rightGestureRecognizer];
    
    _tapGestureRecognizer = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(searchTableViewTaped:)];
    _tapGestureRecognizer.delegate = self;
    
    
    //Uses Retina4 to select correct image
    //self.tableView.backgroundView = [DesignManager appBackgroundView];
    self.tableView.backgroundColor = [UIManager appBackgroundColor];
    //self.tableView.backgroundColor = [UIColor clearColor];
    
    //UIView *backgroundView = [UIView new];
    //backgroundView.backgroundColor = [DesignManager appBackgroundColor];
    //self.tableView.backgroundView = backgroundView;
    //self.view.backgroundColor = [DesignManager appBackgroundColor];
    
    self.tableView.separatorColor = [UIColor colorWithWhite:0.77 alpha:1.0];
    self.tableView.separatorStyle = UITableViewCellSeparatorStyleSingleLine;
#if __IPHONE_OS_VERSION_MAX_ALLOWED >= 70000
    if (IOS7_OR_LATER) {
        [self.tableView setSeparatorInset:UIEdgeInsetsZero];//
    }
#endif
    
    //Hide empty separators
    UIView *v = [[UIView alloc] initWithFrame:CGRectZero];
    v.backgroundColor = [UIColor clearColor];
    self.tableView.tableFooterView = v;

    
    self.refresh = [UIRefreshControl new];
    self.refresh.tintColor = [UIColor whiteColor];
    self.refresh.backgroundColor = [UIManager appBackgroundColor];
    [self.refresh addTarget:self action:@selector(refreshPulled) forControlEvents:UIControlEventValueChanged];
    [self.tableView addSubview:self.refresh];
    
    [self setSearchBarVisible:YES];
    
    [self setDisplayMode:_displayMode];
    
}

- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    
    
    [self.tableView flashScrollIndicators];
}

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

#pragma mark - Refresh
-(void)refreshPulled
{
    /*
    if (self.displayMode == DisplayModeHot){
        if ([DataManager sharedInstance].fetchedContacts) {
            [[DataManager sharedInstance] fetchRemoteHotKnotes];
        } else {
            [self.refresh performSelector:@selector(endRefreshing) withObject:nil afterDelay:1];
        }
    } else if (self.displayMode == DisplayModePeople){
        [[DataManager sharedInstance] fetchRemoteContacts];
    }else{
        [[DataManager sharedInstance] fetchRemoteTopics];
    }*/
    
    [self.refresh endRefreshing];
    [self.tableView reloadData];
}


#pragma mark - Bar Positioning
- (UIBarPosition)positionForBar:(id <UIBarPositioning>)bar
{
    return UIBarPositionTopAttached;
}

#pragma mark - Search
- (void)setSearchBarVisible:(BOOL)visible
{
    BOOL search_present = _searchBar == _tableView.tableHeaderView;
    
    if(visible && !search_present){
        //[[UITextField appearanceWhenContainedIn:[UISearchBar class], nil] setTextColor:[UIColor whiteColor]];
        _tableView.tableHeaderView = _searchBar;
        
        //if (!_haveScrolled) {
        [self.tableView setContentOffset:CGPointMake(0, _tableView.contentOffset.y +  _searchBar.frame.size.height) animated:NO];
        //}
    } else if(!visible && search_present) {
        _tableView.tableHeaderView = [[UIView alloc] initWithFrame:CGRectMake(0.0f, 0.0f, _tableView.bounds.size.width, 0.01f)];
        
    }
}

#pragma mark - Segment control

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
    
    NSLog(@"setDisplayMode from %d to %d animated? %d", oldDisplayMode, displayMode, animated);
    switch (displayMode) {
        case DisplayModeFoods:
            
            _swipeLeftRecognizer.enabled = YES;
            //_swipeRightRecognizer.enabled = YES;
            
            //_tableView.separatorStyle = UITableViewCellSeparatorStyleNone;
            //self.navigationItem.rightBarButtonItem = nil;
            [self setSearchBarVisible:YES];
            self.navigationItem.title = FOODS_TITLE;
            self.navigationItem.rightBarButtonItem = _addButton;
            self.lblTitle.text = @"Search Meals/Foods";
            break;
        case DisplayModeCurrent:
            
            //[self clearEditingCell];
            _swipeLeftRecognizer.enabled = YES;
            //_swipeRightRecognizer.enabled = NO;
            
            //_tableView.separatorStyle = UITableViewCellSeparatorStyleSingleLine;
            //self.navigationItem.rightBarButtonItem = _addButton;
            
            self.navigationItem.title = CURRENT_TITLE;
            self.navigationItem.rightBarButtonItem = nil;
            
            [self setSearchBarVisible:NO];
            
            //if (!self.searchingPeople) {
            //    [_peopleSearchResults removeAllObjects];
            //}
            self.lblTitle.text = @"Current Meals/Foods";
            self.viewStatus.hidden = NO;
            break;
        case DisplayModeFavorites:

            _swipeLeftRecognizer.enabled = NO;
            //_swipeRightRecognizer.enabled = NO;
            
            //_tableView.separatorStyle = UITableViewCellSeparatorStyleNone;
            //self.navigationItem.rightBarButtonItem = _addButton;
            [self setSearchBarVisible:YES];
            //[_topicSearchResults removeAllObjects];
            self.navigationItem.title = FAVORITES_TITLE;
            self.navigationItem.rightBarButtonItem = nil;
            
            //if(_autoKnote){
            //    _autoKnote = NO;
            //    [self performSelector:@selector(startAddTopic) withObject:nil afterDelay:0.25];
            //}
            self.lblTitle.text = @"Favourites Meals/Foods";
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
    
    UITableViewRowAnimation rowAnimation = oldDisplayMode < _displayMode ?
    UITableViewRowAnimationLeft : UITableViewRowAnimationRight;
    
    
    self.transitioningData = YES;
    self.oldDisplayMode = oldDisplayMode;
    
    if(!animated){
        [self.tableView reloadData];
    } else {
        [self.tableView reloadSections:[NSIndexSet indexSetWithIndex:0] withRowAnimation:rowAnimation];
    }
    [self updateViewConstraints];
    
    //[self.tableView setContentOffset:CGPointZero animated:YES];
    
}

- (void)updateViewConstraints {
    [super updateViewConstraints];
    
    // constraints
   
}


- (void)scrollUp
{
    [self.tableView scrollToRowAtIndexPath:[NSIndexPath indexPathForRow:NSNotFound inSection:0] atScrollPosition:UITableViewScrollPositionTop animated:YES];
}

#pragma mark - Edge Swiping

- (void)screenEdgeSwipedLeft:(UIScreenEdgePanGestureRecognizer *)recognizer
{
    if(recognizer.state == UIGestureRecognizerStateBegan){
        
        if(self.displayMode == DisplayModeFavorites){
            self.displayMode = DisplayModeCurrent;
        } else if(self.displayMode == DisplayModeCurrent){
            self.displayMode = DisplayModeFoods;
        }
    }
}

- (void)screenEdgeSwipedRight:(UIScreenEdgePanGestureRecognizer *)recognizer
{
    if(recognizer.state == UIGestureRecognizerStateBegan){
        
        if(self.displayMode == DisplayModeFoods){
            self.displayMode = DisplayModeCurrent;
        } else if(self.displayMode == DisplayModeCurrent){
            //if(_userContact){
              //  self.currentContact = _userContact;
            //}
            self.displayMode = DisplayModeFavorites;
        }
        
    }
}

#pragma mark - table editing
- (void)setEditing:(BOOL)editing atIndexPath:indexPath cell:(UITableViewCell *)cell
{
    [self setEditing:editing atIndexPath:indexPath cell:cell animate:YES];
}

- (void)setEditing:(BOOL)editing atIndexPath:(NSIndexPath *)indexPath cell:(UITableViewCell *)cell animate:(BOOL)animate
{
    /*
    if ([cell isKindOfClass:[ContactCell class]] || [cell isKindOfClass:[TopicCell class]]) {
        ContactCell *cCell = (ContactCell *)cell;
        if ([cCell respondsToSelector:@selector(setEditor:animate:)]) {
            [cCell setEditor:editing animate:animate];
        }
        
        NSInteger _oldEditingCount = _editingCount;
        _editingCount += editing ? 1 : -1;
        if(_editingCount < 0) _editingCount = 0;
        
        NSLog(@"CombinedViewController setEditing? %d atIndexPath: %d editing count old: %d new: %d", editing, indexPath.row, _oldEditingCount, _editingCount);
        
        if(editing){
            _editingCell = cCell;
            _editingIndexPath = indexPath;
            _swipeLeftRecognizer.enabled = NO;
        } else if(_editingCount == 0){
            _editingCell = nil;
            _editingIndexPath = nil;
            _swipeLeftRecognizer.enabled = YES;
        }
    }
    */
}

#pragma mark - gesture

- (void)panRight:(UISwipeGestureRecognizer *)recognizer
{
    /*
    NSLog(@".");
    if(recognizer.state == UIGestureRecognizerStateEnded){
        NSLog(@"panRight state %d", (int)recognizer.state);
        
        if (_displayMode == DisplayModeCurrent) {
            if (recognizer.direction == UISwipeGestureRecognizerDirectionLeft) {
                [self myTopics:YES];
                recognizer.enabled = NO;
                
            }
            
        } else  if (_displayMode == DisplayModeFoods) {
            //[self hotKnoteSwiped:YES recognizer:recognizer];
        }
    }
     */
}

- (void)swipedRight:(UISwipeGestureRecognizer *)recognizer
{
    /*
     if (_displayMode == DisplayModeHot) {
     [self hotKnoteSwiped:NO recognizer:recognizer];
     }
     */
    
}

-(void)addPressed {
    /*
    if(_displayMode == DisplayModePeople){
        [self startAddPerson];
    } else{
        [self startAddTopic];
    }
     */
}

- (void)onBack:(id)sender {
    [self.navigationController popViewControllerAnimated:YES];
}


#pragma mark - Table view data source

- (NSMutableArray *)dataForTable:(UITableView *)tableView
{
    DisplayMode mode = _displayMode;
    if(self.transitioningData){
        mode = _oldDisplayMode;
    }
    if (tableView == _tableView) {
        switch (mode){
            case DisplayModeFoods:
                return self.foods;
            case DisplayModeCurrent:
                return self.currentFoods;
            case DisplayModeFavorites:
                return self.favoritesFoods;
        }
    }
    else if (tableView == _searchController.searchResultsTableView) {
        switch (mode){
            case DisplayModeFoods:
                return self.foodsSearchResults;
            case DisplayModeCurrent:
                return self.currentFoodsSearchResults;
            case DisplayModeFavorites:
                return self.favoritesFoodsSearchResults;
            default:
                break;
                
        }
    }
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
    }
    return cell;
}

- (UITableViewCell *)tableView:(UITableView *)tableView foodsCellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    
    FoodTableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:FoodsCellIdentifier];
    if (cell == nil)
    {
        cell = [[FoodTableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:FoodsCellIdentifier];
    }
    
    Food *food = (Food *)[self dataForTable:tableView][indexPath.row];
    [cell bind:food];
    return cell;
     
    /*
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CurrentCellIdentifier];
    if (cell == nil)
    {
        cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CurrentCellIdentifier];
    }
    
    Food *food = (Food *)[self dataForTable:tableView][indexPath.row];
    cell.textLabel.text = food.name;
    return cell;
     */
}

- (UITableViewCell *)tableView:(UITableView *)tableView currentCellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    
     FoodTableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CurrentCellIdentifier];
     if (cell == nil)
     {
     cell = [[FoodTableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CurrentCellIdentifier];
     }
     
     Food *food = (Food *)[self dataForTable:tableView][indexPath.row];
     [cell bind:food];
     return cell;
    
    
    /*
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CurrentCellIdentifier];
    if (cell == nil)
    {
        cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CurrentCellIdentifier];
    }
    
    Food *food = (Food *)[self dataForTable:tableView][indexPath.row];
    cell.textLabel.text = food.name;
    return cell;
     */
}

- (UITableViewCell *)tableView:(UITableView *)tableView favoritesCellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    FoodTableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:FavoritesCellIdentifier];
    if (cell == nil)
    {
        cell = [[FoodTableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:FavoritesCellIdentifier];
    }
    
    Food *food = (Food *)[self dataForTable:tableView][indexPath.row];
    [cell bind:food];
    return cell;
    /*
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:FavoritesCellIdentifier];
    if (cell == nil)
    {
        cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:FavoritesCellIdentifier];
    }
    
    Food *food = (Food *)[self dataForTable:tableView][indexPath.row];
    cell.textLabel.text = food.name;
    return cell;
     */
}

#pragma mark - table view delegate

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    /*
    self.expIndexPath = nil;
    switch (_displayMode){
        case DisplayModePeople:
            [self openContactRowInTableView:tableView atIndexPath:indexPath autoStartKnote:NO];
            break;
        case DisplayModeSpaces:
            [self openTopicRowInTableView:tableView atIndexPath:indexPath];
            break;
        case DisplayModeHot:
            [self openMessageRowInTableView:tableView atIndexPath:indexPath];
            break;
    }
     */
}
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

#pragma mark - UISearchDisplayDelegate methods

- (void)updateSearchResultsForString:(NSString *)searchString
{
    //self.searchString = searchString;
    NSMutableArray *searchResults = nil;
    switch (_displayMode) {
        case DisplayModeFoods:
            searchResults = _foodsSearchResults;
            break;
        case DisplayModeCurrent:
            searchResults = _currentFoodsSearchResults;
            break;
        case DisplayModeFavorites:
            searchResults = _favoritesFoodsSearchResults;
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
    _searchMode = YES;
}
- (void)searchDisplayController:(UISearchDisplayController *)controller willHideSearchResultsTableView:(UITableView *)tableView
{
    _searchMode = NO;
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

#pragma mark - UIGestureRecognizerDelegate
- (BOOL)gestureRecognizer:(UIGestureRecognizer *)gestureRecognizer shouldRecognizeSimultaneouslyWithGestureRecognizer:(UIGestureRecognizer *)otherGestureRecognizer {
    return NO; // Recognizers of this class are the first priority
}

@end
