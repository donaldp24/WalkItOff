//
//  RegisterViewController.m
//  WalkItOff
//
//  Created by Donald Pae on 6/7/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "RegisterViewController.h"
#import "UIManager.h"

@interface RegisterViewController () {
    UIBarButtonItem *_backButton;
    UIResponder *currentResponder;
}

@property (nonatomic, strong) IBOutlet UIButton *btnCreateAccount;
@property (nonatomic, strong) IBOutlet UIButton *btnContinueWithoutLogin;
@property (nonatomic, strong) IBOutlet UIImageView *ivMale;
@property (nonatomic, strong) IBOutlet UIImageView *ivFemale;
@property (nonatomic, strong) IBOutlet UIButton *btnMale;
@property (nonatomic, strong) IBOutlet UIButton *btnFemale;

@end

@implementation RegisterViewController

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
    
    
    // configure buttons
    [self.btnCreateAccount setTintColor:[UIColor whiteColor]];
    [self.btnCreateAccount setTitle:@"Create Account" forState:UIControlStateNormal];
    self.btnCreateAccount.backgroundColor = [UIColor colorWithRed:255/255.0 green:107/255.0 blue:108/255.0 alpha:1.0];
    self.btnCreateAccount.layer.cornerRadius = 5.0;
    [self.btnCreateAccount addTarget:self action:@selector(onCreateAccount:) forControlEvents:UIControlEventTouchUpInside];
    
    //
    [self.btnContinueWithoutLogin setTintColor:[UIColor colorWithRed:38/255.0 green:38/255.0 blue:38/255.0 alpha:1.0]];
    [self.btnContinueWithoutLogin setTitle:@"Continue Without Logging In" forState:UIControlStateNormal];
    self.btnContinueWithoutLogin.backgroundColor = [UIColor colorWithRed:244/255.0 green:204/255.0 blue:102/255.0 alpha:1.0];
    self.btnContinueWithoutLogin.layer.cornerRadius = 5.0;
    [self.btnContinueWithoutLogin addTarget:self action:@selector(onContinueWithoutLogin:) forControlEvents:UIControlEventTouchUpInside];
    
    self.navigationController.navigationBarHidden = NO;
    self.navigationItem.hidesBackButton = YES;
    
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

/*
#pragma mark - Navigation

// In a storyboard-based application, you will often want to do a little preparation before navigation
- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender
{
    // Get the new view controller using [segue destinationViewController].
    // Pass the selected object to the new view controller.
}
*/

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

#pragma mark - Actions
- (IBAction)onCreateAccount:(id)sender
{
    [self.navigationController popViewControllerAnimated:YES];
}

- (IBAction)onContinueWithoutLogin:(id)sender
{
    [self.navigationController popViewControllerAnimated:YES];
}

- (void)onBack:(id)sender
{
    [self.navigationController popViewControllerAnimated:YES];
}

- (IBAction)onMale:(id)sender
{
    self.btnMale.selected = YES;
    self.ivMale.image = [UIImage imageNamed:@"selectedcircle"];
    
    self.btnFemale.selected = NO;
    self.ivFemale.image = [UIImage imageNamed:@"blankcircle"];
}

- (IBAction)onFemale:(id)sender
{
    self.btnMale.selected = NO;
    self.ivMale.image = [UIImage imageNamed:@"blankcircle"];
    
    self.btnFemale.selected = YES;
    self.ivFemale.image = [UIImage imageNamed:@"selectedcircle"];
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
