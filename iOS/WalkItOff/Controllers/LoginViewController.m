//
//  LoginViewController.m
//  WalkItOff
//
//  Created by Donald Pae on 6/7/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "LoginViewController.h"
#import "NSString+Knotable.h"
#import "RegisterViewController.h"
#import "Model.h"
#import "SVProgressHUD+walkitoff.h"
#import "AppContext.h"
#import "AppDelegate.h"
#import "UserContext.h"
#import "AppSettings.h"
#import "CommonMethods.h"
#import "NSDate+walkitoff.h"

#define verticalGap 3.0
#define ktDefaultLoginTimeInterval 20.0

static CGFloat logoLowerPos = 74.0;
static CGFloat logoUpperPos = 38.0;

static CGFloat textFieldsLowerPos = 200.0;
static CGFloat textFieldsUpperPos = 160.0;

typedef enum
{
    LoginStateLoggingIn,
    LoginStateForgotPassword
} LoginState;

enum  {
    INPUT_NAME = 0,
    INPUT_NAME_EXISTS,
    INPUT_PASSWORD,
    INPUT_PASSWORD_TOO_SHORT,
    INPUT_EMAIL,
    INPUT_EMAIL_INVALID,
    INPUT_EMAIL_EXISTS,
    INPUT_CONNECTION_PROBLEM,
    INPUT_OK
};

@interface LoginViewController () {
    LoginState loginState;
    UIResponder *currentResponder;
    
    NSString *_inputPassword;
    NSString *_inputEmail;
}



@property (weak, nonatomic) IBOutlet UIImageView *imgBg;
@property (strong, nonatomic) UIImageView *logo;
@property (strong, nonatomic) UIView *loginGroup;

@property (strong, nonatomic) UITextField *passwordTextField;
@property (strong, nonatomic) UITextField *emailTextField;

@property (strong, nonatomic) UIButton *submitButton;
@property (strong, nonatomic) UIButton *bottomRightButton;
@property (strong, nonatomic) UIButton *bottomLeftButton;
@property (strong, nonatomic) UIImageView *verticalDivider;

@property (strong, nonatomic) UIButton *loginFacebookButton;

@property (strong, nonatomic) UIButton *continueWithoutLogin;

@property (strong, nonatomic) MASConstraint *loginGroupTopConstraint;
@property (strong, nonatomic) MASConstraint *passwordFieldTopConstraint;
@property (strong, nonatomic) MASConstraint *bottomLeftButtonRightConstraint;


@property (nonatomic, strong) NSTimer *checkTimer;




@end

@implementation LoginViewController

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
    
    
    self.logo = [[UIImageView alloc] initWithImage:[UIImage imageNamed:@"logo"]];
    [self.view addSubview:self.logo];
    
    [_logo mas_makeConstraints:^(MASConstraintMaker *make) {
        make.top.equalTo(@(logoLowerPos));
        make.centerX.equalTo(@0);
    }];
    
    
    UIView *bottomGroup = [[UIView alloc] initWithFrame:CGRectZero];
    bottomGroup.backgroundColor = [UIColor clearColor];
    //bottomGroup.alpha = 0.5;
    [self.view addSubview:bottomGroup];
    
    _verticalDivider = [[UIImageView alloc] initWithImage:[UIImage imageNamed:@"vertical-divider"]];
    
    _bottomRightButton = [UIButton buttonWithType:UIButtonTypeSystem];
    [_bottomRightButton setTitle:@"PASSWORD" forState:UIControlStateNormal];
    [_bottomRightButton addTarget:self action:@selector(enterForgotPassword:) forControlEvents:UIControlEventTouchUpInside];
    _bottomRightButton.tintColor = [UIColor darkGrayColor];
    _bottomRightButton.titleLabel.font = [UIFont systemFontOfSize:12.0];
    
    _bottomLeftButton = [UIButton buttonWithType:UIButtonTypeSystem];
    [_bottomLeftButton setTitle:@"SIGN UP" forState:UIControlStateNormal];
    [_bottomLeftButton addTarget:self action:@selector(enterSignup:) forControlEvents:UIControlEventTouchUpInside];
    _bottomLeftButton.tintColor = _bottomRightButton.tintColor;
    _bottomLeftButton.titleLabel.font = _bottomRightButton.titleLabel.font;
    
    [bottomGroup addSubview:_bottomRightButton];
    [bottomGroup addSubview:_bottomLeftButton];
    [bottomGroup addSubview:_verticalDivider];
    
    
    [bottomGroup mas_makeConstraints:^(MASConstraintMaker *make) {
        //make.bottom.equalTo(@-108);
        make.top.equalTo(@380);
        make.left.equalTo(@0);
        make.right.equalTo(@0);
        
        // make.height.equalTo(@30.0);
    }];
    
    
    [_verticalDivider mas_makeConstraints:^(MASConstraintMaker *make) {
        make.top.equalTo(@4);
        make.bottom.equalTo(@-4);
        make.centerX.equalTo(@0);
        
    }];
    
    [_bottomLeftButton mas_makeConstraints:^(MASConstraintMaker *make) {
        self.bottomLeftButtonRightConstraint = make.right.equalTo(_verticalDivider.mas_left).with.offset(-8.0);
        make.centerY.equalTo(_verticalDivider);
        // make.left.equalTo(@0);
    }];
    
    [_bottomRightButton mas_makeConstraints:^(MASConstraintMaker *make) {
        make.left.equalTo(_verticalDivider.mas_right).with.offset(8.0);
        make.centerY.equalTo(_verticalDivider);
        // make.right.equalTo(@0);
        
    }];
    
    [self initializeTextFields];
    
    _continueWithoutLogin = [UIButton buttonWithType:UIButtonTypeSystem];
    [_continueWithoutLogin setTintColor:[UIColor colorWithRed:38/255.0 green:38/255.0 blue:38/255.0 alpha:1.0]];
    [_continueWithoutLogin setTitle:@"Continue Without Loggin In" forState:UIControlStateNormal];
    
    _continueWithoutLogin.backgroundColor = [UIColor colorWithRed:244.0/255.0 green:244.0/255.0 blue:244.0/255.0 alpha:1.0];
    _continueWithoutLogin.layer.cornerRadius = 5.0;
    _continueWithoutLogin.layer.borderColor = [UIColor colorWithRed:193/255.0 green:193/255.0 blue:193/255.0 alpha:1.0].CGColor;
    _continueWithoutLogin.layer.borderWidth = 0.5f;
    
    [_continueWithoutLogin addTarget:self action:@selector(onContinueWithoutLogin:) forControlEvents:UIControlEventTouchUpInside];
    
    [self.view addSubview:_continueWithoutLogin];
    
    [_continueWithoutLogin mas_makeConstraints:^(MASConstraintMaker *make) {
        make.height.equalTo(@42.0);
        make.width.equalTo(@260.0);
        make.centerX.equalTo(self.view);
        make.bottom.equalTo(self.view).offset(-90);
    }];
    
    _loginFacebookButton = [UIButton buttonWithType:UIButtonTypeCustom];
    [_loginFacebookButton setImage:[UIImage imageNamed:@"loginfacebook"] forState:UIControlStateNormal];
    [_loginFacebookButton setTitle:@"" forState:UIControlStateNormal];
    [_loginFacebookButton addTarget:self action:@selector(onLoginWithFacebook:) forControlEvents:UIControlEventTouchUpInside];
    
    [self.view addSubview:_loginFacebookButton];
    
    [_loginFacebookButton mas_makeConstraints:^(MASConstraintMaker *make) {
        make.height.equalTo(@42.0);
        make.width.equalTo(@260.0);
        make.centerX.equalTo(self.view);
        make.top.equalTo(_continueWithoutLogin.mas_bottom).offset(10);
    }];
    
    
    UITapGestureRecognizer *tap = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(backgroundTap:)];
    [self.view addGestureRecognizer:tap];

    // login
    User *user = [UserContext loadUser];
    if (user != nil)
    {
        if (user.type == UserTypeNormal)
        {
            self.emailTextField.text = user.email;
            //self.passwordTextField.text = user.pwd;
            
            if([UserContext getDefaultLogin])
            {
                SHOW_PROGRESS(@"Please wait");
                [User loginUserWithEmail:user.email pwd:user.pwd success:^(User *user) {
                    
                    [SVProgressHUD dismiss];
                    //[SVProgressHUD dismissWithSuccess:@"Success" afterDelay:2.0];
                    
                    // save current user to context
                    [UserContext saveUser:user];
                    
                    [User setCurrentUser:user];
                    
                    [self gotoMain];
                    
                    //
                } failure:^(NSString *msg) {
                    [SVProgressHUD dismissWithError:(([NSString stringWithFormat:@"Login failed : %@", msg])) afterDelay:2.0];
                }];
            }
        }
        else if (user.type == UserTypeFacebook)
        {
            if ([UserContext getDefaultLogin]) {
                if (FBSession.activeSession.state == FBSessionStateCreatedTokenLoaded) {
                    
                    // If there's one, just open the session silently, without showing the user the login UI
                    BOOL bRet = [FBSession openActiveSessionWithReadPermissions:@[@"public_profile", @"email"]
                                                                   allowLoginUI:NO
                                                                 completionHandler:^(FBSession *session, FBSessionState state, NSError *error) {
                                                      // Handler for session state changes
                                                      // This method will be called EACH time the session state changes,
                                                      // also for intermediate states and NOT just when the session open
                                                      [self sessionStateChanged:session state:state error:error];
                                                  }];
                    bRet = bRet;
                }
            }
        }
        else if (user.type == UserTypeNoneAuth)
        {
            if ([UserContext getDefaultLogin])
            {
                [User setCurrentUser:user];
                [self gotoMain];
            }
        }
        
        // request public permission (public actions)
        if (user.type != UserTypeFacebook)
        {
            // facebook auth open
            if (FBSession.activeSession.state == FBSessionStateCreatedTokenLoaded) {
                [FBSession openActiveSessionWithReadPermissions:@[@"public_profile", @"email"]
                                                   allowLoginUI:NO
                                              completionHandler:^(FBSession *session, FBSessionState state, NSError *error) {
                                                  
                                                  // request public_actions permission
                                                  // Check for publish permissions
                                                  [FBRequestConnection startWithGraphPath:@"/me/permissions"
                                                                        completionHandler:^(FBRequestConnection *connection, id result, NSError *error) {
                                                                            //__block NSString *alertText;
                                                                            //__block NSString *alertTitle;
                                                                            if (!error){
                                                                                NSDictionary *permissions= [(NSArray *)[result data] objectAtIndex:0];
                                                                                if (![permissions objectForKey:@"publish_actions"]){
                                                                                    // Publish permissions not found, ask for publish_actions
                                                                                    [self requestPublishPermissions];
                                                                                    
                                                                                } else {
                                                                                    // Publish permissions found, publish the OG story
                                                                                    //[self publishStory];
                                                                                }
                                                                                
                                                                            } else {
                                                                                // There was an error, handle it
                                                                                // See https://developers.facebook.com/docs/ios/errors/
                                                                            }
                                                                        }];

                                              }];
            }
        }
    }
}

- (void)viewWillAppear:(BOOL)animated
{
    [super viewWillAppear:animated];
    
    self.navigationController.navigationBarHidden = YES;
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

- (void) initializeTextFields {
    
    _emailTextField = [self loginTextFieldForIcon:@"login-email" placeholder:@"EMAIL"];
    _emailTextField.keyboardType = UIKeyboardTypeEmailAddress;
    _emailTextField.autocapitalizationType = UITextAutocapitalizationTypeNone;
    _emailTextField.autocorrectionType = UITextAutocorrectionTypeNo;
    _emailTextField.spellCheckingType = UITextSpellCheckingTypeNo;
    
    _passwordTextField = [self loginTextFieldForIcon:@"login-password" placeholder:@"PASSWORD"];
    _passwordTextField.secureTextEntry = YES;
    _passwordTextField.autocapitalizationType = UITextAutocapitalizationTypeNone;
    _passwordTextField.autocorrectionType = UITextAutocorrectionTypeNo;
    _passwordTextField.spellCheckingType = UITextSpellCheckingTypeNo;
    
    //usernameTextField.alpha = _passwordTextField.alpha = _emailTextField.alpha = 0.5;
    
    _submitButton = [UIButton buttonWithType:UIButtonTypeSystem];
    //_submitButton.backgroundColor = [UIColor colorWithRed:0.21 green:0.68 blue:0.90 alpha:1.0];
    _submitButton.backgroundColor = [UIColor colorWithRed:255/255.0 green:107/255.0 blue:108/255.0 alpha:1.0];
    
    _submitButton.tintColor = [UIColor whiteColor];
    _submitButton.layer.cornerRadius = 5.0;
    [_submitButton setTitle:@"LOGIN" forState:UIControlStateNormal];
    [_submitButton addTarget:self action:@selector(onLogin:) forControlEvents:UIControlEventTouchUpInside];
    
    
    _loginGroup = [UIView new];
    _loginGroup.backgroundColor = [UIColor clearColor];
    
    [_loginGroup addSubview:_emailTextField];
    [_loginGroup addSubview:_passwordTextField];
    [_loginGroup addSubview:_submitButton];
    
    
    
    [self.view addSubview:_loginGroup];
    
    
    [_emailTextField mas_makeConstraints:^(MASConstraintMaker *make) {
        make.height.equalTo(@42.0);
        make.width.equalTo(@260.0);
        make.top.equalTo(@0);
        make.left.equalTo(@0);
        make.right.equalTo(@0);
    }];
    
    [_passwordTextField mas_makeConstraints:^(MASConstraintMaker *make) {
        make.size.equalTo(_emailTextField);
        make.left.equalTo(_emailTextField);
        self.passwordFieldTopConstraint = make.top.equalTo(_emailTextField.mas_bottom).with.offset(verticalGap);
    }];
    
    
    
    [_submitButton mas_makeConstraints:^(MASConstraintMaker *make) {
        make.size.equalTo(_passwordTextField);
        make.left.equalTo(_passwordTextField);
        make.top.equalTo(_passwordTextField.mas_bottom).with.offset(15.0);
        make.bottom.equalTo(@0);
    }];
    
    [_loginGroup mas_makeConstraints:^(MASConstraintMaker *make) {
        make.centerX.equalTo(@0);
        _loginGroupTopConstraint = make.top.equalTo(@(textFieldsLowerPos));
    }];
}

- (UITextField *)loginTextFieldForIcon:(NSString *)filename placeholder:(NSString *)placeholder {
    
    //Gray background view
    UIView *grayView = [[UIView alloc] initWithFrame:CGRectMake(0, 0, 45.0, 42.0)];
    grayView.backgroundColor = [UIColor colorWithRed:0.67 green:0.70 blue:0.77 alpha:1.0];
    
    //Path & Mask so we only make rounded corners on right side
    UIBezierPath *maskPath = [UIBezierPath bezierPathWithRoundedRect:grayView.bounds
                                                   byRoundingCorners:(UIRectCornerTopLeft | UIRectCornerBottomLeft)
                                                         cornerRadii:CGSizeMake(5.0, 5.0)];
    CAShapeLayer *maskLayer = [[CAShapeLayer alloc] init];
    maskLayer.frame = grayView.bounds;
    maskLayer.path = maskPath.CGPath;
    grayView.layer.mask = maskLayer;
    
    //Add icon image
    UIImageView *passwordIcon = [[UIImageView alloc] initWithImage:[UIImage imageNamed:filename]];
    [grayView addSubview:passwordIcon];
    
    [passwordIcon mas_makeConstraints:^(MASConstraintMaker *make) {
        make.centerX.equalTo(@0);
        make.centerY.equalTo(@0);
    }];
    
    //Finally make the textField
    UITextField *textField = [[UITextField alloc] init];
    textField.borderStyle = UITextBorderStyleRoundedRect;
    textField.font = [UIFont systemFontOfSize:14.0];
    textField.textColor = [UIColor blackColor];
    textField.backgroundColor = [UIColor whiteColor];
    textField.leftViewMode = UITextFieldViewModeAlways;
    textField.leftView = grayView;
    textField.placeholder = placeholder;
    textField.delegate = self;
    
    return textField;
}


# pragma mark Gesture selector
- (void)backgroundTap:(UITapGestureRecognizer *)backgroundTap {
    if(currentResponder){
        [currentResponder resignFirstResponder];
    }
}

#pragma mark Login

- (IBAction)onLogin:(id)sender {
    if (currentResponder) {
        [currentResponder resignFirstResponder];
    }
#if true
    int nInput = [self getInputType];
    
    if (nInput != INPUT_OK) {
        [self showAlertMessage:nInput];
    } else {
        SHOW_PROGRESS(@"Please wait");
        [User loginUserWithEmail:self.emailTextField.text pwd:self.passwordTextField.text success:^(User *user) {
            
            [SVProgressHUD dismiss];
            //[SVProgressHUD dismissWithSuccess:@"Success" afterDelay:2.0];
            
            // save current user to context
            [UserContext saveUser:user];
            
            [User setCurrentUser:user];
            
            [self gotoMain];
            
            //           
        } failure:^(NSString *msg) {
            [SVProgressHUD dismissWithError:(([NSString stringWithFormat:@"Login failed : %@", msg])) afterDelay:2.0];
        }];
    }
#else
    [self performSegueWithIdentifier:@"gotoMain" sender:self];
#endif
}

- (void)gotoMain
{
    [AppSettings initSettingsWithUserUid:[User currentUser].uid];
    
    // start pedometer if it is already started, have to stop when log out
    AppContext *context = [AppContext initContext:[User currentUser].uid];
    if (context.pedometerStarted)
    {
        AppDelegate *appDelegate = (AppDelegate *)[UIApplication sharedApplication].delegate;
        [appDelegate.pedometer start];
    }
    [context save];
    
    // set is logged in
    [UserContext sharedContext].isLoggedIn = YES;
    [UserContext setDefaultLogin];
    
    // jump to main
    [self performSegueWithIdentifier:@"gotoMain" sender:self];
}

- (void)gotoMainWithoutLogin
{
    [AppSettings initSettingsWithUserUid:[User currentUser].uid];
    
    // start pedometer if it is already started, have to stop when log out
    AppContext *context = [AppContext initContext:[User currentUser].uid];
    if (context.pedometerStarted)
    {
        AppDelegate *appDelegate = (AppDelegate *)[UIApplication sharedApplication].delegate;
        [appDelegate.pedometer start];
    }
    [context save];
    
    // set is logged in
    [UserContext sharedContext].isLoggedIn = YES;
    [UserContext saveUser:[User currentUser]];
    [UserContext clearDefaultLogin];
    
    // jump to main
    [self performSegueWithIdentifier:@"gotoMain" sender:self];

}

- (void)prepareForEnteringLoginState {
    loginState = LoginStateLoggingIn;
    
    [_passwordFieldTopConstraint uninstall];
    [_passwordTextField mas_makeConstraints:^(MASConstraintMaker *make) {
        self.passwordFieldTopConstraint = make.top.equalTo(_emailTextField.mas_bottom).with.offset(verticalGap);
    }];
    
    [_logo mas_updateConstraints:^(MASConstraintMaker *make) {
        make.top.equalTo(@(logoLowerPos));
    }];
    
    [_loginGroup mas_updateConstraints:^(MASConstraintMaker *make) {
        make.top.equalTo(@(textFieldsLowerPos));
    }];
}

- (void)configureLoginState {
    loginState = LoginStateLoggingIn;
    
    [self reset];
    
    //Without these there is an unwanted fade animation
    [UIView setAnimationsEnabled:NO];
    [_bottomLeftButton setTitle:@"SIGN UP" forState:UIControlStateNormal];
    [_bottomRightButton setTitle:@"PASSWORD" forState:UIControlStateNormal];
    [_submitButton setTitle:@"LOGIN" forState:UIControlStateNormal];
    [UIView setAnimationsEnabled:YES];
    
   
    [_bottomLeftButton addTarget:self action:@selector(enterSignup:) forControlEvents:UIControlEventTouchUpInside];
    [_bottomRightButton addTarget:self action:@selector(enterForgotPassword:) forControlEvents:UIControlEventTouchUpInside];
    [_submitButton addTarget:self action:@selector(onLogin:) forControlEvents:UIControlEventTouchUpInside];
}

- (void)leaveLoginState {
    [_bottomLeftButton removeTarget:self action:@selector(enterSignup:) forControlEvents:UIControlEventTouchUpInside];
    [_bottomRightButton removeTarget:self action:@selector(enterForgotPassword:) forControlEvents:UIControlEventTouchUpInside];
    [_submitButton removeTarget:self action:@selector(onLogin:) forControlEvents:UIControlEventTouchUpInside];
}


#pragma mark Forgot / Rest Password

- (void)enterForgotPassword:(id)sender {
    [self leaveLoginState];
    
    loginState = LoginStateForgotPassword;
    
    _emailTextField.hidden = NO;
    
    [_logo mas_updateConstraints:^(MASConstraintMaker *make) {
        make.top.equalTo(@(logoUpperPos));
    }];
    _bottomLeftButtonRightConstraint.offset( -8 + (_bottomLeftButton.bounds.size.width / 2.0) );
    /*
     [_bottomLeftButton mas_makeConstraints:^(MASConstraintMaker *make) {
     make.right.equalTo(@0.0);
     }];
     */
    
    _loginGroupTopConstraint.offset(textFieldsUpperPos);
    
    
    [UIView animateWithDuration:0.3 animations:^{
        _passwordTextField.alpha = 0;
        _verticalDivider.alpha = 0;
        _bottomRightButton.alpha = 0;
        [self.view layoutIfNeeded];
        [self.view layoutIfNeeded];
        
    } completion:^(BOOL finished) {
        
        [UIView setAnimationsEnabled:NO];
        [_bottomLeftButton setTitle:@"BACK" forState:UIControlStateNormal];
        //[_bottomRightButton setTitle:@"" forState:UIControlStateNormal];
        [_submitButton setTitle:@"RESET PASSWORD" forState:UIControlStateNormal];
        
        [UIView setAnimationsEnabled:YES];
        
        [_bottomLeftButton addTarget:self action:@selector(exitForgotPassword:) forControlEvents:UIControlEventTouchUpInside];
        //[_bottomRightButton addTarget:self action:@selector(exitForgotPassword:) forControlEvents:UIControlEventTouchUpInside];
        [_submitButton addTarget:self action:@selector(resetPassword) forControlEvents:UIControlEventTouchUpInside];
        
    }];
    
    
}

- (void)resetPassword {
    if (currentResponder) {
        [currentResponder resignFirstResponder];
    }
    
    int nInput = [self getInputType];
    
    if (nInput != INPUT_OK) {
        [self showAlertMessage:nInput];
    } else {
        
        SHOW_PROGRESS(@"Please Wait");
        [User resetPasswordWithEmail:self.emailTextField.text success:^() {
            HIDE_PROGRESS_WITH_SUCCESS(@"Sent a mail");
        } failure:^(NSString *msg) {
            HIDE_PROGRESS_WITH_FAILURE(([NSString stringWithFormat:@"Failure : %@", msg]));
        }];
        
    }
    
}

- (void)exitForgotPassword:(id)sender {
    [_bottomLeftButton removeTarget:self action:@selector(exitForgotPassword:) forControlEvents:UIControlEventTouchUpInside];
    [_bottomRightButton removeTarget:self action:@selector(exitForgotPassword:) forControlEvents:UIControlEventTouchUpInside];
    [_submitButton removeTarget:self action:@selector(resetPassword) forControlEvents:UIControlEventTouchUpInside];
    
    
    [_logo mas_updateConstraints:^(MASConstraintMaker *make) {
        make.top.equalTo(@(logoLowerPos));
    }];
    
    _bottomLeftButtonRightConstraint.offset(-8);
    
    _loginGroupTopConstraint.offset(textFieldsLowerPos);
    
    
    [UIView animateWithDuration:0.3 animations:^{
        _passwordTextField.alpha = 1.0;
        _verticalDivider.alpha = 1.0;
        _bottomRightButton.alpha = 1.0;
        
        [self.view layoutIfNeeded];
        
    } completion:^(BOOL finished) {
        [self configureLoginState];
    }];
    
}

#pragma mark sign up
- (IBAction)enterSignup:(id)sender {
    if (currentResponder)
        [currentResponder resignFirstResponder];
    [self performSegueWithIdentifier:@"gotoRegister" sender:self];
}

#pragma mark continue without login
- (void)onContinueWithoutLogin:(id)sender {
    
    User *user = [UserContext loadUser];
    if (user != nil)
    {
        if (user.type == UserTypeNoneAuth)
        {
            // this is the local user
        }
        else
        {
            user = [[User alloc] init];
        }
    }
    else
    {
        user = [[User alloc] init];
    }
    
    [User setCurrentUser:user];
    
    
    NSLog(@"onContinueWithoutLogin...");
    [self gotoMainWithoutLogin];
    
}

#pragma mark login with facebook
- (void)onLoginWithFacebook:(id)sender {
    NSLog(@"onLoginWithFacebook");
    
    // If the session state is any of the two "open" states when the button is clicked
    if (FBSession.activeSession.state == FBSessionStateOpen
        || FBSession.activeSession.state == FBSessionStateOpenTokenExtended) {
        
        NSLog(@"session is exist, so first clear session...");
        
        // Close the session and remove the access token from the cache
        // The session state handler (in the app delegate) will be called automatically
        [FBSession.activeSession closeAndClearTokenInformation];
    }
    
    [FBSession openActiveSessionWithReadPermissions:@[@"public_profile", @"email"]
                                       allowLoginUI:YES
                                       completionHandler:^(FBSession *session, FBSessionState status, NSError *error) {
  
        [self sessionStateChanged:session state:status error:error];
        
    }];
}

// This method will handle ALL the session state changes in the app
- (void)sessionStateChanged:(FBSession *)session state:(FBSessionState) state error:(NSError *)error
{
//    return;
    
    SHOW_PROGRESS(@"Please Wait");
    // If the session was opened successfully
    if (!error && state == FBSessionStateOpen)
    {
        NSLog(@"Session opened");
        // Request to get information
        [[FBRequest requestForMe] startWithCompletionHandler:^(FBRequestConnection *connection, NSDictionary<FBGraphUser>* result, NSError *error) {
            
            NSLog(@"fetching information...");

            if (!error)
            {
                NSLog(@"fetched information successfully! %@, %@, %@", result[@"email"], result[@"id"], result[@"name"]);
                
                User *user = [self getUserFromGraphUser:result];
                [[NSOperationQueue mainQueue] addOperationWithBlock:^(){
                [User loginUserWithFacebook:(User *)user success:^(User *user) {
                    [SVProgressHUD dismiss];
                    // save current user to context
                    [UserContext saveUser:user];
                    
                    [User setCurrentUser:user];
                    [self performSelectorOnMainThread:@selector(gotoMain) withObject:nil waitUntilDone:NO];
                    //[self gotoMain];
                } failure:^(NSString *msg) {
                    HIDE_PROGRESS_WITH_FAILURE(([NSString stringWithFormat:@"Login failed : %@", msg]));
                }];}];
                
                // request public_actions permission
                // Check for publish permissions
                [FBRequestConnection startWithGraphPath:@"/me/permissions"
                                      completionHandler:^(FBRequestConnection *connection, id result, NSError *error) {
                                          //__block NSString *alertText;
                                          //__block NSString *alertTitle;
                                          if (!error){
                                              NSDictionary *permissions= [(NSArray *)[result data] objectAtIndex:0];
                                              if (![permissions objectForKey:@"publish_actions"]){
                                                  // Publish permissions not found, ask for publish_actions
                                                  [self requestPublishPermissions];
                                                  
                                              } else {
                                                  // Publish permissions found, publish the OG story
                                                  //[self publishStory];
                                              }
                                              
                                          } else {
                                              // There was an error, handle it
                                              // See https://developers.facebook.com/docs/ios/errors/
                                          }
                                      }];
                
            }
            else
            {
                NSLog(@"fatching information failed : error = %@", [error localizedDescription]);
                HIDE_PROGRESS_WITH_FAILURE(@"Getting user info failed.");
            }
          
        }];

        return;
    }
    
    HIDE_PROGRESS_WITH_FAILURE(@"Login failed");
    
    
    if (state == FBSessionStateClosed || state == FBSessionStateClosedLoginFailed){
        // If the session is closed
        NSLog(@"Session closed");
        // Show the user the logged-out UI
        
    }
    
    // Handle errors
    if (error){
        NSLog(@"Error");
        NSString *alertText;
        NSString *alertTitle;
        // If the error requires people using an app to make an action outside of the app in order to recover
        if ([FBErrorUtility shouldNotifyUserForError:error] == YES){
            alertTitle = @"Something went wrong";
            alertText = [FBErrorUtility userMessageForError:error];
            //[self showMessage:alertText withTitle:alertTitle];
        } else {
            
            // If the user cancelled login, do nothing
            if ([FBErrorUtility errorCategoryForError:error] == FBErrorCategoryUserCancelled) {
                NSLog(@"User cancelled login");
                
                // Handle session closures that happen outside of the app
            } else if ([FBErrorUtility errorCategoryForError:error] == FBErrorCategoryAuthenticationReopenSession){
                alertTitle = @"Session Error";
                alertText = @"Your current session is no longer valid. Please log in again.";
                //[self showMessage:alertText withTitle:alertTitle];
                NSLog(@"%@", alertText);
                
                // Here we will handle all other errors with a generic error message.
                // We recommend you check our Handling Errors guide for more information
                // https://developers.facebook.com/docs/ios/errors/
            } else {
                //Get more error information from the error
                NSDictionary *errorInformation = [[[error.userInfo objectForKey:@"com.facebook.sdk:ParsedJSONResponseKey"] objectForKey:@"body"] objectForKey:@"error"];
                
                // Show the user an error message
                alertTitle = @"Something went wrong";
                alertText = [NSString stringWithFormat:@"Please retry. \n\n If the problem persists contact us and mention this error code: %@", [errorInformation objectForKey:@"message"]];
                //[self showMessage:alertText withTitle:alertTitle];
                NSLog(@"%@", alertText);
            }
        }
        // Clear this token
        [FBSession.activeSession closeAndClearTokenInformation];
        // Show the user the logged-out UI
        //[self userLoggedOut];
    }
}

- (User *)getUserFromGraphUser:(NSDictionary *)graphUser
{
    User *user = [[User alloc] init];
    user.type = UserTypeFacebook;
    user.uid = 0;
    if ([graphUser objectForKey:@"name"])
        user.name = [graphUser objectForKey:@"name"];
    else
        user.name = @"";
    if ([graphUser objectForKey:@"email"])
        user.email = [graphUser objectForKey:@"email"];
    else
        user.email = @"";
    if ([graphUser objectForKey:@"gender"])
        user.gender = ([[graphUser objectForKey:@"gender"] caseInsensitiveCompare:@"male"] == NSOrderedSame)?0:1;
    else
        user.gender = 0;
    
    user.token = [graphUser objectForKey:@"id"];
    
    NSString* birth = [graphUser objectForKey:@"birthday"];
    if (birth != nil && birth.length >= 4)
    {
        NSString *year = [birth substringFromIndex:birth.length - 4];
        int nyear = [year intValue];
        NSString *currYear = [[CommonMethods date2str:[NSDate date] withFormat:DATE_FORMAT] substringToIndex:4];
        int nCurrYear = [currYear intValue];
        user.age = (nCurrYear - nyear > 0) ? (nCurrYear - nyear) : 30;
    }
    
    return user;
}

- (void)requestPublishPermissions
{
    // Request publish_actions
    [FBSession.activeSession requestNewPublishPermissions:[NSArray arrayWithObject:@"publish_actions"]
                                          defaultAudience:FBSessionDefaultAudienceOnlyMe
                                        completionHandler:^(FBSession *session, NSError *error) {
                                            __block NSString *alertText;
                                            __block NSString *alertTitle;
                                            if (!error) {
                                                if ([FBSession.activeSession.permissions
                                                     indexOfObject:@"publish_actions"] == NSNotFound){
                                                    // Permission not granted, tell the user we will not publish
                                                    alertTitle = @"Permission not granted";
                                                    alertText = @"Your action will not be published to Facebook.";
                                                    //[[[UIAlertView alloc] initWithTitle:alertTitle                                                                                 message:alertText                                                                                delegate:self                                                                       cancelButtonTitle:@"OK!"                                                                       otherButtonTitles:nil] show];
                                                    NSLog(@"%@", alertText);
                                                } else {
                                                    // Permission granted, publish the OG story
                                                    //[self publishStory];
                                                }
                                                
                                            } else {
                                                // There was an error, handle it
                                                // See https://developers.facebook.com/docs/ios/errors/
                                            }
                                        }];

}

#pragma mark Input Validation

- (void) updateAndCleanInput {

    _inputEmail = [[_emailTextField.text trimmed] lowercaseStringWithLocale:[NSLocale currentLocale]];
    _emailTextField.text = _inputEmail;
    
    NSArray *emailComponents = [_inputEmail componentsSeparatedByString:@"@"];
    //_inputFullname = emailComponents.count > 0 ? emailComponents.firstObject : _inputEmail;
    
    _inputPassword = _passwordTextField.text;
}

- (int) getInputType {
    int nRet;
    
    [self updateAndCleanInput];
    
    switch (loginState) {
        case LoginStateForgotPassword:
            nRet = [self validateForgotPassword];
            break;
        default:
            //LoginStateLoggingIn
            nRet = [self validateLoggingIn];
            break;
    }
    return nRet;
}


- (int) validateForgotPassword {
    if (_inputEmail.length == 0) {
        return INPUT_EMAIL;
    }
    else if (![_inputEmail isValidEmail]) {
        return INPUT_EMAIL_INVALID;
    }
    return INPUT_OK;
}

- (int)validateLoggingIn {
    int nRet;
    if (_inputEmail.length == 0) {
        nRet = INPUT_EMAIL;
    } else if (_inputPassword.length == 0) {
        nRet = INPUT_PASSWORD;
    } else {
        nRet = INPUT_OK;
    }
    return nRet;
}

- (int)validateSigningUp {
    int nRet;
    
    if (_inputEmail.length == 0) {
        nRet = INPUT_EMAIL;
    }
    else if (![_inputEmail isValidEmail]) {
        nRet = INPUT_EMAIL_INVALID;
    }
    else if (_inputPassword.length == 0) {
        nRet = INPUT_PASSWORD;
    }
    else if (_inputPassword.length < 6) {
        nRet = INPUT_PASSWORD_TOO_SHORT;
    }
    else {
        nRet = INPUT_OK;
    }
    return nRet;
}

- (void) showAlertMessage:(int) type {
    NSString* strTitle;
    switch (type) {
        case INPUT_CONNECTION_PROBLEM:
            strTitle = @"We're sorry, there is a network issue. Please try again later";
            break;
        case INPUT_NAME:
            strTitle = loginState == LoginStateLoggingIn ? @"Please enter your name" : @"Please enter a name";
            break;
        case INPUT_NAME_EXISTS:
            strTitle = @"That username is taken, please choose another";
            break;
        case INPUT_PASSWORD:
            strTitle = loginState == LoginStateLoggingIn ? @"Please enter your password" : @"Please enter a password";
            break;
        case INPUT_PASSWORD_TOO_SHORT:
            strTitle = @"That password is too short, it must be at least 6 characters";
            break;
        case INPUT_EMAIL:
            strTitle = @"Please enter your email address";
            break;
        case INPUT_EMAIL_INVALID:
            strTitle = @"That email address is not valid";
            break;
        case INPUT_EMAIL_EXISTS:
            strTitle = @"That email is already being used, please log in";
            break;
        default:
            strTitle = @"";
            break;
    }
    UIAlertView* alert = [[UIAlertView alloc] initWithTitle:strTitle message:nil delegate:self cancelButtonTitle:@"OK" otherButtonTitles:nil];
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
    /*
     if (textField == _usernameTextField && preFilledUsername) {
     preFilledUsername = NO;
     textField.text = @"";
     }
     */
}

- (void)textFieldDidEndEditing:(UITextField *)textField {
    currentResponder = nil;
}


- (void) reset
{
    /*
     if(self.currentAccount && loginState == LoginStateLoggingIn){
     _usernameTextField.text = self.currentAccount.user.name;
     preFilledUsername = YES;
     } else {
     _usernameTextField.text = @"";
     preFilledUsername = NO;
     }
     */
    
    _passwordTextField.text = @"";
    _emailTextField.text = @"";
}

#pragma mark Keyboard Methods

- (void)keyboardShowing:(NSNotification *)note
{
    NSNumber *duration = note.userInfo[UIKeyboardAnimationDurationUserInfoKey];
    //CGRect endFrame = ((NSValue *)note.userInfo[UIKeyboardFrameEndUserInfoKey]).CGRectValue;
    _loginGroupTopConstraint.with.offset(60.0);
    
    
    [UIView animateWithDuration:duration.floatValue animations:^{
        self.logo.alpha = 0.0;
        [self.view layoutIfNeeded];
    }];
}

- (void)keyboardHiding:(NSNotification *)note
{
    NSNumber *duration = note.userInfo[UIKeyboardAnimationDurationUserInfoKey];
    
    _loginGroupTopConstraint.with.offset(loginState == LoginStateLoggingIn ? textFieldsLowerPos : textFieldsUpperPos);
    
    [UIView animateWithDuration:duration.floatValue animations:^{
        self.logo.alpha = 1.0;
        [self.view layoutIfNeeded];
    }];
    
}



@end
