//
//  CombinedViewController.h
//  WalkItOff
//
//  Created by Donald Pae on 6/13/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "SwipeTableView.h"

typedef enum _DisplayMode
{
    DisplayModeFoods,
    DisplayModeCurrent,
    DisplayModeFavorites
} DisplayMode;




@interface CombinedViewController : UIViewController
<
UITableViewDataSource,
UITableViewDelegate,
UIGestureRecognizerDelegate,
UISearchBarDelegate,
UISearchDisplayDelegate,
SwipeTableViewDelegate,
UIBarPositioningDelegate
>

@property (strong, nonatomic) UIRefreshControl *refresh;

- (void)initDisplayMode:(DisplayMode)mode;

@end
