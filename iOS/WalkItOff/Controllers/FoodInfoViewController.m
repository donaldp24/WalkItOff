//
//  FoodInfoViewController.m
//  WalkItOff
//
//  Created by Donald Pae on 6/16/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "FoodInfoViewController.h"
#import "UIManager.h"
#import "CustomButton.h"
#import "Model.h"
#import "Formulas+walkitoff.h"
#import "AutoMessageBox.h"
#import "SVProgressHUD+walkitoff.h"


@interface FoodInfoViewController () {
    UIBarButtonItem *_backButton;
}

@property (nonatomic, strong) IBOutlet UIButton *btnFavorites;

@property (nonatomic, strong) IBOutlet UILabel *lblName;
@property (nonatomic, strong) IBOutlet UILabel *lblCalories;
@property (nonatomic, strong) IBOutlet UILabel *lblSteps;
@property (nonatomic, strong) IBOutlet UILabel *lblMiles;

@end

@implementation FoodInfoViewController

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
    
    if (self.food)
    {
        self.lblName.text = self.food.name;
        self.lblCalories.text = [NSString stringWithFormat:@"%d Calories", (int)self.food.calories];
        
        // calculate steps and miles
        CGFloat steps = [Formulas stepsToBeWalkedToBurnFood:self.food.calories userCaloriesBurnedPerStep:[Formulas userCaloriesBurnedPerStep:[Formulas userCaloriesBurnedPerMile:[Formulas weightInLbsWithKg:[User currentUser].weight]] strideLengthInMiles:[Formulas userStrideLengthInMiles:[User currentUser].height]]];
        
        CGFloat miles = steps * [Formulas userStrideLengthInMiles:[User currentUser].height];
        
        self.lblSteps.text = [NSString stringWithFormat:@"%d Steps", (int)steps];
        
        self.lblMiles.text = [NSString stringWithFormat:@"%.2f Miles", miles];
        
    }
    
    for (Food *food in [User currentUser].favoritesFoods) {
        if (food.uid == self.food.uid &&
            food.useruid == self.food.useruid &&
            food.isCustom == self.food.isCustom)
        {
            self.btnFavorites.enabled = NO;
            break;
        }
    }

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

#pragma mark - Data source
- (NSMutableArray *)dataForCurrentFoods
{
    return [User currentUser].currentFoods;
}

- (NSMutableArray *)dataForFavoritesFoods
{
    return [User currentUser].favoritesFoods;
}

#pragma mark - Actions
- (IBAction)onAddToCurrent:(id)sender
{
    if ([[self dataForCurrentFoods] containsObject:self.food])
        return;
#ifdef _USE_REMOTE
    [Food addFoodToCurrent:[User currentUser].uid food:self.food success:^(){
        [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
            [[self dataForCurrentFoods] addObject:self.food];
        }];
    } failure:^(NSString *msg) {
        //
    }];
#else
    
    SHOW_PROGRESS(@"Please Wait");
    
    [CurrentFood addFoodToCurrentWithLocal:[User currentUser].uid food:self.food success:^(){
        
        //
        [CurrentFood getCurrentFoodsWithLocal:[User currentUser].uid isConsumed:NO success:^(NSMutableArray *foods) {
            
            [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
                [User currentUser].currentFoods = foods;
                //[AutoMessageBox AutoMsgInView:self withText:@"Success" withSuccess:YES];
                HIDE_PROGRESS_WITH_SUCCESS(@"Success");
                
            }];
            
        } failure: ^(NSString *msg) {
            [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
                //[AutoMessageBox AutoMsgInView:self withText:@"Failure" withSuccess:NO];
                HIDE_PROGRESS_WITH_FAILURE(([NSString stringWithFormat:@"Failure : %@", msg]));
            }];
        }];
        
    } failure:^(NSString *msg) {
        HIDE_PROGRESS_WITH_FAILURE(([NSString stringWithFormat:@"Failure : %@", msg]));
    }];

#endif
}

- (IBAction)onAddToFavorites:(id)sender
{
    if ([[self dataForFavoritesFoods] containsObject:self.food])
        return;
    
#ifdef _USE_REMOTE
    [Food addFoodToFavorites:[User currentUser].uid food:self.food success:^() {
        [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
            [[self dataForFavoritesFoods] addObject:self.food];
        }];
    } failure:^(NSString *msg) {
        //
    }];
#else
    SHOW_PROGRESS(@"Please Wait");
    [Food addFoodToFavoritesWithLocal:[User currentUser].uid food:self.food success:^(){
        [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
            [[self dataForFavoritesFoods] addObject:self.food];
            self.btnFavorites.enabled = NO;
            //[AutoMessageBox AutoMsgInView:self withText:@"Success" withSuccess:YES];
            HIDE_PROGRESS_WITH_SUCCESS(@"Success");
        }];
    } failure:^(NSString *msg) {
        [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
            //[AutoMessageBox AutoMsgInView:self withText:@"Failure" withSuccess:NO];
            HIDE_PROGRESS_WITH_FAILURE(([NSString stringWithFormat:@"Failure : %@", msg]));
        }];
    }];
#endif
    
}

- (void)onBack:(id)sender
{
    [self.navigationController popViewControllerAnimated:YES];
}



@end
