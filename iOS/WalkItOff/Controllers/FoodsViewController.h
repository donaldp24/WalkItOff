//
//  FoodsViewController.h
//  WalkItOff
//
//  Created by Donald Pae on 6/15/14.
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


@interface FoodsViewController : UIViewController
    <
    UITableViewDataSource,
    UITableViewDelegate,
    //UIGestureRecognizerDelegate
    SwipeTableViewDelegate,
    UITextFieldDelegate
    >


- (void)initDisplayMode:(DisplayMode) mode;


@end
