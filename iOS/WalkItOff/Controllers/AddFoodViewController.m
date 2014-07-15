//
//  AddFoodViewController.m
//  WalkItOff
//
//  Created by Donald Pae on 6/15/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "AddFoodViewController.h"
#import "UIManager.h"
#import "Model.h"
#import "AutoMessageBox.h"
#import "SVProgressHUD+walkitoff.h"

@interface AddFoodViewController () {
    UIBarButtonItem *_backButton;
    UIResponder *currentResponder;
}

@property (nonatomic, strong) IBOutlet UITextField *txtName;
@property (nonatomic, strong) IBOutlet UITextField *txtCalories;

@end

@implementation AddFoodViewController

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
    
    UITapGestureRecognizer *tap = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(backgroundTap:)];
    [self.view addGestureRecognizer:tap];

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

#pragma mark - Actions
- (IBAction)onAdd:(id)sender
{
    if (currentResponder)
        [currentResponder resignFirstResponder];
    // add food to custom food
    if (self.txtName.text == nil || self.txtName.text.length <= 0)
    {
        // alert
        [self showAlertWithMessage:@"Please input name of food/meal"];
        return;
    }
    
    if (self.txtCalories.text == nil || self.txtCalories.text.length == 0)
    {
        [self showAlertWithMessage:@"Please input calories of food/meal"];
        return;
    }
    SHOW_PROGRESS(@"Please Wait");
    // save custom food/meal to db
    [Food addCustomFoodWithLocal:[User currentUser].uid name:self.txtName.text calories:[self.txtCalories.text floatValue] success:^() {
//        [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
//            [AutoMessageBox AutoMsgInView:self withText:@"Success" withSuccess:YES];
//        }];
        HIDE_PROGRESS_WITH_SUCCESS(@"Success");
    }failure:^(NSString *msg) {
//        [[NSOperationQueue mainQueue] addOperationWithBlock:^() {
//            [AutoMessageBox AutoMsgInView:self withText:@"Failure" withSuccess:NO];
//        }];
        HIDE_PROGRESS_WITH_FAILURE(@"Failure");
    }];
    
}

- (void)onBack:(id)sender
{
    [self.navigationController popViewControllerAnimated:YES];
}

- (void)showAlertWithMessage:(NSString *)msg
{
    UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"" message:msg delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
    [alert show];
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

# pragma mark Gesture selector
- (void)backgroundTap:(UITapGestureRecognizer *)backgroundTap {
    if(currentResponder){
        [currentResponder resignFirstResponder];
    }
}

@end
