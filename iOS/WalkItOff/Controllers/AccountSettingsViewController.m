//
//  AccountSettingsViewController.m
//  WalkItOff
//
//  Created by Donald Pae on 6/29/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "AccountSettingsViewController.h"
#import "UIManager.h"
#import "User.h"
#import "UserContext.h"
#import "SVProgressHUD+walkitoff.h"
#import "NSString+Knotable.h"

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

@interface AccountSettingsViewController () {
    UIBarButtonItem *_backButton;
    UIResponder *currentResponder;
}

@property (nonatomic, strong) IBOutlet UITextField *txtName;
@property (nonatomic, strong) IBOutlet UITextField *txtAge;
@property (nonatomic, strong) IBOutlet UITextField *txtWeight;
@property (nonatomic, strong) IBOutlet UITextField *txtHeight;
@property (nonatomic, strong) IBOutlet UITextField *txtEmail;
@property (nonatomic, strong) IBOutlet UITextField *txtPwd;
@property (nonatomic, strong) IBOutlet UITextField *txtPwdConfirm;

@property (nonatomic, strong) IBOutlet UIImageView *ivMale;
@property (nonatomic, strong) IBOutlet UIImageView *ivFemale;
@property (nonatomic, strong) IBOutlet UIButton *btnMale;
@property (nonatomic, strong) IBOutlet UIButton *btnFemale;

@end

@implementation AccountSettingsViewController

- (id)initWithStyle:(UITableViewStyle)style
{
    self = [super initWithStyle:style];
    if (self) {
        // Custom initialization
    }
    return self;
}

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    // Uncomment the following line to preserve selection between presentations.
    // self.clearsSelectionOnViewWillAppear = NO;
    
    // Uncomment the following line to display an Edit button in the navigation bar for this view controller.
    // self.navigationItem.rightBarButtonItem = self.editButtonItem;
    
    //Hide empty separators
    UIView *v = [[UIView alloc] initWithFrame:CGRectZero];
    v.backgroundColor = [UIColor clearColor];
    self.tableView.tableFooterView = v;
    
    self.tableView.backgroundColor = [UIManager appBackgroundColor];
    
    // back button
    
    _backButton = [[UIBarButtonItem alloc] initWithImage:[UIImage imageNamed:@"backicon"] style:UIBarButtonItemStylePlain target:self action:@selector(onBack:)];
    self.navigationItem.leftBarButtonItem = _backButton;
    
    // set values to control
    User *user = [User currentUser];
    self.txtName.text = user.name;
    self.txtAge.text = [NSString stringWithFormat:@"%d", user.age];
    if (user.weight == (int)user.weight)
        self.txtWeight.text = [NSString stringWithFormat:@"%d", (int)user.weight];
    else
        self.txtWeight.text = [NSString stringWithFormat:@"%.1f", user.weight];
    if (user.height == (int)user.height)
        self.txtHeight.text = [NSString stringWithFormat:@"%d", (int)user.height];
    else
        self.txtHeight.text = [NSString stringWithFormat:@"%.1f", user.height];
    self.txtEmail.text = user.email;
    self.txtPwd.text = user.pwd;
    self.txtPwdConfirm.text = user.pwd;
    if (user.gender == 0)
        [self onMale:nil];
    else
        [self onFemale:nil];
    
    if (user.type == UserTypeNormal)
    {
        [self.txtEmail setTextColor:[UIColor lightGrayColor]];
        self.txtEmail.enabled = NO;
    }
    
    UITapGestureRecognizer *tap = [[UITapGestureRecognizer alloc] initWithTarget:self action:@selector(backgroundTap:)];
    [self.view addGestureRecognizer:tap];

}

- (void)didReceiveMemoryWarning
{
    [super didReceiveMemoryWarning];
    // Dispose of any resources that can be recreated.
}

#pragma mark - Table view data source

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{
    // Return the number of sections.
    return 1;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    // Return the number of rows in the section.
    return 9;
}

/*
- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:<#@"reuseIdentifier"#> forIndexPath:indexPath];
    
    // Configure the cell...
    
    return cell;
}
*/

/*
// Override to support conditional editing of the table view.
- (BOOL)tableView:(UITableView *)tableView canEditRowAtIndexPath:(NSIndexPath *)indexPath
{
    // Return NO if you do not want the specified item to be editable.
    return YES;
}
*/

/*
// Override to support editing the table view.
- (void)tableView:(UITableView *)tableView commitEditingStyle:(UITableViewCellEditingStyle)editingStyle forRowAtIndexPath:(NSIndexPath *)indexPath
{
    if (editingStyle == UITableViewCellEditingStyleDelete) {
        // Delete the row from the data source
        [tableView deleteRowsAtIndexPaths:@[indexPath] withRowAnimation:UITableViewRowAnimationFade];
    } else if (editingStyle == UITableViewCellEditingStyleInsert) {
        // Create a new instance of the appropriate class, insert it into the array, and add a new row to the table view
    }   
}
*/

/*
// Override to support rearranging the table view.
- (void)tableView:(UITableView *)tableView moveRowAtIndexPath:(NSIndexPath *)fromIndexPath toIndexPath:(NSIndexPath *)toIndexPath
{
}
*/

/*
// Override to support conditional rearranging of the table view.
- (BOOL)tableView:(UITableView *)tableView canMoveRowAtIndexPath:(NSIndexPath *)indexPath
{
    // Return NO if you do not want the item to be re-orderable.
    return YES;
}
*/

/*
#pragma mark - Navigation

// In a storyboard-based application, you will often want to do a little preparation before navigation
- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender
{
    // Get the new view controller using [segue destinationViewController].
    // Pass the selected object to the new view controller.
}
*/

#pragma mark - navigation item
- (void)onBack:(id)sender {
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

- (IBAction)onSave:(id)sender
{
    if (currentResponder)
        [currentResponder resignFirstResponder];
    
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
    
    // get values from control
    
    NSString *name = [self.txtName.text trimmed];
    int age = [[self.txtAge.text trimmed] intValue];
    int gender = (self.btnFemale.selected)?1:0;
    CGFloat weight = [[self.txtWeight.text trimmed] floatValue];
    CGFloat height = [[self.txtHeight.text trimmed] floatValue];
    NSString *email = [self.txtEmail.text trimmed];
    NSString *pwd = self.txtPwd.text;
    NSString *pwdConfrm = self.txtPwdConfirm.text;
    // check validation
    
    
    User *user = [[User alloc] init];
    user.uid = [User currentUser].uid;
    user.name = name;
    user.age = age;
    user.gender = gender;
    user.weight = weight;
    user.height = height;
    user.email = email;
    user.pwd = pwd;
    user.token = [User currentUser].token;
    user.type = [User currentUser].type;
    
    
    if ([User currentUser].type == UserTypeNoneAuth)
    {
        SHOW_PROGRESS(@"Please Wait");
        // save user to context
        User *currentUser = [User currentUser];
        currentUser.name = user.name;
        currentUser.age = user.age;
        currentUser.gender = user.gender;
        currentUser.email = user.email;
        currentUser.weight = user.weight;
        currentUser.height = user.height;
        currentUser.pwd = user.pwd;
        [UserContext saveUser:currentUser];
        HIDE_PROGRESS_WITH_SUCCESS(@"Success");
    }
    else
    {
        SHOW_PROGRESS(@"Please Wait");
        // save user to sever
        [User updateUser:user success:^(User *user) {
            HIDE_PROGRESS_WITH_SUCCESS(@"Success");
            [User setCurrentUser:user];
        }failure:^(NSString *msg) {
            HIDE_PROGRESS_WITH_SUCCESS(@"Failure");
        }];
    }
    
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
