//
//  FoodsViewController.h
//  WalkItOff
//
//  Created by Donald Pae on 6/15/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import <UIKit/UIKit.h>

#import "SwipeTableView.h"
#import "Model.h"
#import "AppDelegate.h"

typedef enum _DisplayMode
{
    DisplayModeFoods,
    DisplayModeCurrent,
    DisplayModeFavorites
} DisplayMode;

@protocol FoodsViewCellsDelegate <NSObject>

@optional
- (void)onFoodCellBtnPlus:(Food *)food;
- (void)onCurrentCellBtnMinus:(Food *)food;
- (void)onFavoritesCellBtnPlus:(Food *)food;
- (void)onFavoritesCellBtnMinus:(Food *)food;

@end


@interface FoodsViewController : UIViewController
    <
    UITableViewDataSource,
    UITableViewDelegate,
    UIGestureRecognizerDelegate,
//    SwipeTableViewDelegate,
    UITextFieldDelegate,
    PedometerViewerDelegate,
    FoodsViewCellsDelegate
    >


- (void)initDisplayMode:(DisplayMode) mode;


@end
