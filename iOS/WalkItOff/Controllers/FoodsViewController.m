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


@interface FoodsViewController () {
    UIBarButtonItem *_addButton;
    UIBarButtonItem *_backButton;
    UIResponder *currentResponder;
    
    BOOL firstLoaded;
}

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
@property (nonatomic, strong) NSMutableArray *currentFoods;
@property (nonatomic, strong) NSMutableArray *favoritesFoods;

@property (nonatomic, strong) NSMutableArray *foodsSearchResults;


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
    food.name = @"Portion of Chips";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Pint of beer";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Bowl of Cereal";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Chocolate Bar 100g";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Cheeseburger";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Can of Cola";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Chips";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Chocolate";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Biscuits";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Burger";
    food.calories = 0.3;
    
    [self.foods addObject:food];
    [self.currentFoods addObject:food];
    [self.favoritesFoods addObject:food];
    food = [[Food alloc] init];
    food.uid = 0;
    food.name = @"Beer";
    food.calories = 0.3;
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
    self.currentFoods = [[NSMutableArray alloc] init];
    self.favoritesFoods = [[NSMutableArray alloc] init];
    
    self.foodsSearchResults = [[NSMutableArray alloc] init];

    
    [self initTempData];
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
    
    
    // setup table view
    //Hide empty separators
    UIView *v = [[UIView alloc] initWithFrame:CGRectZero];
    v.backgroundColor = [UIColor clearColor];
    self.tblFoods.tableFooterView = v;
    self.tblCurrents.tableFooterView = v;
    self.tblFavorites.tableFooterView = v;
    
    
    self.refresh = [UIRefreshControl new];
    self.refresh.tintColor = [UIColor whiteColor];
    self.refresh.backgroundColor = [UIManager appBackgroundColor];
    [self.refresh addTarget:self action:@selector(refreshPulled) forControlEvents:UIControlEventValueChanged];
    
    [self.tblFoods addSubview:self.refresh];
    
    self.tblCurrents.tableHeaderView = v;
    self.tblFavorites.tableHeaderView = v;
    
    firstLoaded = YES;
    
    
    UITapGestureRecognizer *tap = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(backgroundTap:)];
    [self.view addGestureRecognizer:tap];
    
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
    if (scrollView != self.swipeView)
        return;
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
    if (scrollView != self.swipeView)
        return;
    
    CGFloat width = scrollView.frame.size.width;
    NSInteger page = (scrollView.contentOffset.x + (0.5f * width)) / width;
    
    
    self.segmentedControl.selectedSegmentIndex = page;
    [self.segmentedControl sendActionsForControlEvents:UIControlEventValueChanged];
    
    UITableView *tableView = [self tableViewForMode:self.displayMode];
    [tableView flashScrollIndicators];
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
    [self.tblFoods reloadData];
}


#pragma mark - Bar Positioning
- (UIBarPosition)positionForBar:(id <UIBarPositioning>)bar
{
    return UIBarPositionTopAttached;
}

#pragma mark - Table view data source

- (NSMutableArray *)dataForTable:(UITableView *)tableView
{
    DisplayMode mode = _displayMode;
    if(self.transitioningData){
        mode = _oldDisplayMode;
    }
    if (tableView == self.tblFoods)
        return self.foods;
    else if (tableView == self.tblCurrents)
        return self.currentFoods;
    else if (tableView == self.tblFavorites)
        return self.favoritesFoods;
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
    
    CurrentTableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:CurrentCellIdentifier];
    if (cell == nil)
    {
        cell = [[CurrentTableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:CurrentCellIdentifier];
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
    FavoritesTableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:FavoritesCellIdentifier];
    if (cell == nil)
    {
        cell = [[FavoritesTableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:FavoritesCellIdentifier];
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
    FoodTableViewCell *cell = [tableView cellForRowAtIndexPath:indexPath];
    Food *food = cell.food;
    
    FoodInfoViewController *vc = [self.storyboard instantiateViewControllerWithIdentifier:@"FoodInfoViewController"];
    [self.navigationController pushViewController:vc animated:YES];
}

- (void)openCurrentRowInTableView:(UITableView *)tableView atIndexPath:(NSIndexPath *)indexPath
{
    FoodTableViewCell *cell = [tableView cellForRowAtIndexPath:indexPath];
    Food *food = cell.food;
    
    FoodInfoViewController *vc = [self.storyboard instantiateViewControllerWithIdentifier:@"FoodInfoViewController"];
    [self.navigationController pushViewController:vc animated:YES];
}

- (void)openFavoritesRowInTableView:(UITableView *)tableView atIndexPath:(NSIndexPath *)indexPath
{
    FoodTableViewCell *cell = [tableView cellForRowAtIndexPath:indexPath];
    Food *food = cell.food;
    
    FoodInfoViewController *vc = [self.storyboard instantiateViewControllerWithIdentifier:@"FoodInfoViewController"];
    [self.navigationController pushViewController:vc animated:YES];
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
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(keyboardShowing:)
                                                 name:UIKeyboardWillShowNotification
                                               object:nil];
    [[NSNotificationCenter defaultCenter] addObserver:self
                                             selector:@selector(keyboardHiding:)
                                                 name:UIKeyboardWillHideNotification
                                               object:nil];
}
- (void)viewWillDisappear:(BOOL)animated
{
    [[NSNotificationCenter defaultCenter] removeObserver:self name:UIKeyboardWillShowNotification object:nil];
    [[NSNotificationCenter defaultCenter] removeObserver:self name:UIKeyboardWillHideNotification object:nil];
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

@end
