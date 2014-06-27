//
//  AddFoodViewController.m
//  WalkItOff
//
//  Created by Donald Pae on 6/15/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "AddFoodViewController.h"
#import "UIManager.h"

@interface AddFoodViewController () {
    UIBarButtonItem *_backButton;
}

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

- (void)onBack:(id)sender
{
    [self.navigationController popViewControllerAnimated:YES];
}

@end
