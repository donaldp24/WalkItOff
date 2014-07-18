//
//  RegisterViewController.m
//  WalkItOff
//
//  Created by Donald Pae on 6/7/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "RegisterViewController.h"
#import "UIManager.h"
#import "NSString+Knotable.h"
#import "Model.h"
#import "SVProgressHUD+walkitoff.h"

typedef enum {
    INPUT_OK,
    INPUT_NAME,
    INPUT_AGE_EMPTY,
    INPUT_GENDER,
    INPUT_WEIGHT_EMPTY,
    INPUT_HEIGHT_EMPTY,
    INPUT_EMAIL_EMPTY,
    INPUT_EMAIL_INCORRECT,
    INPUT_PWD,
    INPUT_PWDCONFIRM,
    INPUT_TYPE,
    INPUT_TOKEN
}INPUT_INVALID;

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

@property (nonatomic, strong) IBOutlet UITextField *txtName;
@property (nonatomic, strong) IBOutlet UITextField *txtAge;
@property (nonatomic, strong) IBOutlet UITextField *txtWeight;
@property (nonatomic, strong) IBOutlet UITextField *txtHeight;
@property (nonatomic, strong) IBOutlet UITextField *txtEmail;
@property (nonatomic, strong) IBOutlet UITextField *txtPwd;
@property (nonatomic, strong) IBOutlet UITextField *txtPwdConfirm;

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

#pragma mark - check validation
- (int)checkValid
{
    NSString *name = [self.txtName.text trimmed];
    NSString *strAge = [self.txtAge.text trimmed];
    NSString *strWeight = [self.txtWeight.text trimmed];
    NSString *strHeight = [self.txtWeight.text trimmed];
    NSString *email = [self.txtEmail.text trimmed];
    NSString *pwd = self.txtPwd.text;
    NSString *pwdConfirm = self.txtPwdConfirm.text;
    if (name.length == 0)
        return INPUT_NAME;
    if (strAge.length == 0)
        return INPUT_AGE_EMPTY;
    
    if (strWeight.length == 0)
        return INPUT_WEIGHT_EMPTY;
    
    if (strHeight.length == 0)
        return INPUT_HEIGHT_EMPTY;
    
    if (email.length == 0)
        return INPUT_EMAIL_EMPTY;
    
    NSArray *emailComponents = [email componentsSeparatedByString:@"@"];
    if (emailComponents == nil || emailComponents.count < 2)
        return INPUT_EMAIL_INCORRECT;
    
    if (pwd.length <= 0)
        return INPUT_PWD;
    
    if (![pwd isEqualToString:pwdConfirm])
        return INPUT_PWDCONFIRM;
    return INPUT_OK;
}

#pragma mark - Actions
- (IBAction)onCreateAccount:(id)sender
{
    if (currentResponder)
        [currentResponder resignFirstResponder];
    
    // check validation
    int ret = [self checkValid];
    NSString *msg = @"";
    switch (ret) {
        case INPUT_NAME:
            msg = @"Please enter your name";
            break;
            
        case INPUT_AGE_EMPTY:
            msg = @"Please enter your age";
            break;
            
        case INPUT_WEIGHT_EMPTY:
            msg = @"Pleaes enter your weight";
            break;
            
        case INPUT_HEIGHT_EMPTY:
            msg = @"Please enter your height";
            break;
            
        case INPUT_EMAIL_EMPTY:
            msg = @"Please enter your email address";
            break;
            
        case INPUT_EMAIL_INCORRECT:
            msg = @"That email address is not valid";
            break;
            
        case INPUT_PWD:
            msg = @"Please enter password";
            break;
            
        case INPUT_PWDCONFIRM:
            msg = @"Password and Confirm password is not equal";
            break;
            
        default:
            break;
    }
    
    if (ret != INPUT_OK)
    {
        UIAlertView* alert = [[UIAlertView alloc] initWithTitle:@"Invalid" message:msg delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil];
        [alert show];
        return;
    }
    
    User *user = [[User alloc] init];
    user.name = [self.txtName.text trimmed];
    user.age = [[self.txtAge.text trimmed] intValue];
    user.gender = (self.btnMale.isSelected)?GenderMale:GenderFemale;
    user.weight = [[self.txtWeight.text trimmed] floatValue];
    user.height = [[self.txtHeight.text trimmed] floatValue];
    user.email = [self.txtEmail.text trimmed];
    user.pwd = self.txtPwd.text;
    user.type = UserTypeNormal;
    user.token = @"";
    
    SHOW_PROGRESS(@"Please wait")
    
    [User registerUser:user success:^(){
        HIDE_PROGRESS_WITH_SUCCESS(@"Created successfully");
        [self performSelector:@selector(onBack:) withObject:nil afterDelay:kSVProgressMsgDelay];
        
    }failure:^(NSString *msg) {
        HIDE_PROGRESS_WITH_FAILURE(([NSString stringWithFormat:@"Create failed : %@", msg]));
    }];
    
    //[self.navigationController popViewControllerAnimated:YES];
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

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{
    return 1;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    return 10;
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
