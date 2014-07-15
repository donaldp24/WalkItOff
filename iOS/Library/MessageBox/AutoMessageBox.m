//
//  AutoMessageBox.m
//  Showhand
//

#import "AutoMessageBox.h"

@interface AutoMessageBox ()

@end

@implementation AutoMessageBox

@synthesize bSuccess;
@synthesize strMsg;

// Set visibility duration
static const CGFloat kDuration = 3;


// Static toastview queue variable
static NSMutableArray *toasts;

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
#pragma mark - Public

+ (void)AutoMsgInView:(UIViewController *)parentViewController withText:(NSString *)text withSuccess:(BOOL)success {
    // Add new instance to queue
    AutoMessageBox *viewCtrl = [[AutoMessageBox alloc] initWithNibName:@"AutoMessageBox" bundle:nil];
    
    viewCtrl.strMsg = text;
    viewCtrl.bSuccess = success;

    
    CGFloat lWidth =  viewCtrl.view.frame.size.width;
    CGFloat lHeight = viewCtrl.view.frame.size.height;
    CGFloat pWidth = parentViewController.view.frame.size.width;
    CGFloat pHeight = parentViewController.view.frame.size.height;
    
    // Change toastview frame
    viewCtrl.view.frame = CGRectMake((pWidth - lWidth) / 2., (pHeight - lHeight) / 2., lWidth, lHeight);
    viewCtrl.view.alpha = 0.0f;
    
    if (toasts == nil) {
        toasts = [[NSMutableArray alloc] initWithCapacity:1];
        [toasts addObject:viewCtrl];
        [AutoMessageBox nextToastInView:parentViewController.view];
    }
    else {
        if (toasts.count <= 0)
            [toasts addObject:viewCtrl];
    }
    
    //[viewCtrl release];
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
#pragma mark - Private

- (void)fadeToastOut {
    
    // Fade in parent view
    [UIView animateWithDuration:0.3 delay:0 options:UIViewAnimationOptionAllowUserInteraction
     
                     animations:^{
                         self.view.alpha = 0.f;
                     }
                     completion:^(BOOL finished){
                         UIView *parentView = self.view.superview;
                         [self.view removeFromSuperview];
                         
                         // Remove current view from array
                         [toasts removeObject:self];
                         if ([toasts count] == 0) {
                            ///[toasts release];
                             toasts = nil;
                         }
                         else
                             [AutoMessageBox nextToastInView:parentView];
                     }];
}


+ (void)nextToastInView:(UIView *)parentView {
    if ([toasts count] > 0) {
        AutoMessageBox *viewCtrl = [toasts objectAtIndex:0];
        
        // Fade into parent view
        [parentView addSubview:viewCtrl.view];
        [UIView animateWithDuration:.5  delay:0 options:UIViewAnimationOptionAllowUserInteraction
                         animations:^{
                             viewCtrl.view.alpha = 1.0;
                         } completion:^(BOOL finished){}];
        
        // Start timer for fade out
        [viewCtrl performSelector:@selector(fadeToastOut) withObject:nil afterDelay:kDuration];
    }
}


- (void)viewDidLoad
{
    [super viewDidLoad];
	// Do any additional setup after loading the view.
    lblMsg.text = strMsg;
    if (bSuccess)
    {
        imgFailure.hidden = YES;
        imgSuccess.hidden = NO;
    }
    else
    {
        imgFailure.hidden = NO;
        imgSuccess.hidden = YES;
    }
    
    self.view.alpha = 0;
        
}

- (void)viewDidUnload
{
    [super viewDidUnload];
    // Release any retained subviews of the main view.
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return (interfaceOrientation == UIInterfaceOrientationPortrait);
}

- (void)dealloc
{
}

#ifdef IOS6

#endif

@end