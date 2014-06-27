//
//  MainTabController.m
//  WalkItOff
//
//  Created by Donald Pae on 6/7/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "MainTabController.h"


#define TABINDEX_HOME       0
#define TABINDEX_PROFILE    1


static MainTabController *_sharedTabController = nil;

@interface MainTabController ()

@property (nonatomic, strong) UIButton *btnHome;
@property (nonatomic, strong) UIButton *btnWalking;
@property (nonatomic, strong) UIButton *btnProfile;

@end

@implementation MainTabController

+ (MainTabController *)sharedTabController
{
    return _sharedTabController;
}

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
    
    self.navigationController.navigationBar.hidden = YES;

    // tabbar controller view
    UIView *view = [[UIView alloc] init];
    CGRect frame = [self.tabBar frame];
    [view setFrame:CGRectMake(0, 0, frame.size.width, frame.size.height)];
    
    // home button
    self.btnHome = [UIButton buttonWithType:UIButtonTypeCustom];
    [self.btnHome setTitle:@"" forState:UIControlStateNormal];
    
    
    UIImage *imgHome = [UIImage imageNamed:@"homebutton"];
    imgHome = [UIImage imageWithCGImage:imgHome.CGImage scale:[UIScreen mainScreen].scale orientation:imgHome.imageOrientation];
    [self.btnHome setImage:[UIImage imageNamed:@"homebutton"] forState:UIControlStateNormal];
    [self.btnHome setImage:[UIImage imageNamed:@"homebutton_selected"] forState:UIControlStateSelected];
    [self.btnHome addTarget:self action:@selector(onHome:) forControlEvents:UIControlEventTouchUpInside];
    [view addSubview:self.btnHome];
    
    // walk button
    UIImage *imgWalk = [UIImage imageNamed:@"startworking"];
    imgWalk = [UIImage imageWithCGImage:imgWalk.CGImage scale:[UIScreen mainScreen].scale orientation:imgWalk.imageOrientation];
    self.btnWalking = [UIButton buttonWithType:UIButtonTypeCustom];
    [self.btnWalking setTitle:@"" forState:UIControlStateNormal];
    
    [self.btnWalking setImage:[UIImage imageNamed:@"startworking"] forState:UIControlStateNormal];
    [self.btnWalking setImage:[UIImage imageNamed:@"stopworking"] forState:UIControlStateSelected];
    [self.btnWalking addTarget:self action:@selector(onWalk:) forControlEvents:UIControlEventTouchUpInside];
    [view addSubview:self.btnWalking];
    
    // profile button
    UIImage *imgProfile = [UIImage imageNamed:@"databutton"];
    imgProfile = [UIImage imageWithCGImage:imgProfile.CGImage scale:[UIScreen mainScreen].scale orientation:imgProfile.imageOrientation];
    self.btnProfile = [UIButton buttonWithType:UIButtonTypeCustom];
    [self.btnProfile setTitle:@"" forState:UIControlStateNormal];
    
    [self.btnProfile setImage:[UIImage imageNamed:@"databutton"] forState:UIControlStateNormal];
    [self.btnProfile setImage:[UIImage imageNamed:@"databutton_selected"] forState:UIControlStateSelected];
    [self.btnProfile addTarget:self action:@selector(onProfile:) forControlEvents:UIControlEventTouchUpInside];
    [view addSubview:self.btnProfile];
    
    [self.btnHome mas_makeConstraints:^(MASConstraintMaker *make) {
        make.left.equalTo(view);
        make.top.equalTo(view);
        make.width.equalTo(@(imgHome.size.width));
        make.height.equalTo(@(imgHome.size.height));
    }];
    
    [self.btnWalking mas_makeConstraints:^(MASConstraintMaker *make) {
        make.left.equalTo(self.btnHome.mas_right);
        make.top.equalTo(view);
        make.width.equalTo(@(imgWalk.size.width));
        make.height.equalTo(@(imgWalk.size.height));
    }];
    
    [self.btnProfile mas_makeConstraints:^(MASConstraintMaker *make) {
        make.left.equalTo(self.btnWalking.mas_right);
        make.top.equalTo(view);
        make.width.equalTo(@(imgProfile.size.width));
        make.height.equalTo(@(imgProfile.size.height));
    }];
    
    
    [self.tabBar addSubview:view];
    
    [self.btnHome setSelected:YES];
    
    _sharedTabController = self;
    
    
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

- (void)onHome:(id)sender
{
    [self setSelectedIndex:TABINDEX_HOME];
    UINavigationController *vc = [[self viewControllers] objectAtIndex:TABINDEX_HOME];
    [vc popToRootViewControllerAnimated:YES];
    [self.btnHome setSelected:YES];
    [self.btnProfile setSelected:NO];
}

- (void)onWalk:(id)sender
{
    self.btnWalking.selected = !self.btnWalking.selected;
}

- (void)onProfile:(id)sender
{
    [self setSelectedIndex:TABINDEX_PROFILE];
    [self.btnProfile setSelected:YES];
    [self.btnHome setSelected:NO];
}

@end
