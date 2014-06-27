//
//  RecordViewController.m
//  WalkItOff
//
//  Created by Donald Pae on 6/16/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "RecordViewController.h"
#import "NSDate+convenience.h"
#import "UIManager.h"

@interface RecordViewController () {
    UIBarButtonItem *_backButton;
}

@property (nonatomic, strong) IBOutlet UIScrollView *swipeView;
@property (nonatomic, strong) IBOutlet UIView *firstView;
@property (nonatomic, strong) IBOutlet UIView *secondView;

@property (nonatomic, strong) IBOutlet UIPageControl *pageCtrl;

@end

@implementation RecordViewController

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
    
    VRGCalendarView *calendar = [[VRGCalendarView alloc] init];
    calendar.delegate=self;
    calendar.animType = CalendarAnimateHorizontal;
    calendar.selectedDate = [NSDate date];
    [self.view addSubview:calendar];
    
    CGRect rt = self.view.frame;
    [self.firstView mas_makeConstraints:^(MASConstraintMaker *make) {
        make.left.equalTo(self.swipeView);
        make.top.equalTo(self.swipeView);
        make.width.equalTo(@(rt.size.width));
        make.bottom.equalTo(self.swipeView);
    }];
    
    [self.secondView mas_makeConstraints:^(MASConstraintMaker *make) {
        make.left.equalTo(self.firstView.mas_right);
        make.top.equalTo(self.swipeView);
        make.right.equalTo(self.swipeView);
        make.bottom.equalTo(self.swipeView);
        make.width.equalTo(@(rt.size.width));
    }];

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

- (void)onBack:(id)sender {
    [self.navigationController popViewControllerAnimated:YES];
}

#pragma mark - Calendar delegate

-(void)calendarView:(VRGCalendarView *)calendarView switchedToMonth:(int)month targetHeight:(float)targetHeight animated:(BOOL)animated {
    if (month==[[NSDate date] month]) {
        NSDate *today = [NSDate date];
        NSArray *dates = [NSArray arrayWithObjects:[NSNumber numberWithInt:[today day]], nil];
        [calendarView markDates:dates];
    }
}

-(void)calendarView:(VRGCalendarView *)calendarView dateSelected:(NSDate *)date {
    NSLog(@"Selected date = %@",date);
}


#pragma mark - Page Scrolling
- (void)scrollViewDidScroll:(UIScrollView *)scrollView
{
    CGFloat width = scrollView.frame.size.width;
    NSInteger page = (scrollView.contentOffset.x + (0.5f * width)) / width;
    
    self.pageCtrl.currentPage = page;
}

- (IBAction)onPageCtrl:(id)sender
{
    CGRect frame = self.swipeView.frame;
    int currPage = self.pageCtrl.currentPage;
    frame.origin.x = frame.size.width * currPage;
    frame.origin.y = 0;
    [self.swipeView scrollRectToVisible:frame animated:YES];
}

@end
