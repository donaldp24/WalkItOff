//
//  UIManager.m
//  WalkItOff
//
//  Created by Donald Pae on 6/11/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "UIManager.h"

static UIManager *_sharedUIManager = nil;

@implementation UIManager


+ (UIManager *)sharedUIManager
{
    if (_sharedUIManager == nil)
        _sharedUIManager = [[UIManager alloc] init];
    return _sharedUIManager;
}

- (NSString *)appTitle
{
    return @"Walk It Off";
}

- (NSInteger)navbarStyle
{
    return UIBarStyleBlackTranslucent;
}

- (UIColor *)navbarTintColor
{
    return [UIColor whiteColor];
}

- (NSDictionary *)navbarTitleTextAttributes
{
    //return @{NSForegroundColorAttributeName:[UIColor whiteColor]};
    //return @{NSForegroundColorAttributeName:[UIColor colorWithRed:199/25.0 green:37/255.0 blue:39/255.0 alpha:1.0]};
    return @{NSForegroundColorAttributeName:[UIColor colorWithRed:255/25.0 green:255/255.0 blue:255/255.0 alpha:1.0]};
}

- (UIColor *)navbarBarTintColor
{
    //return [UIColor colorWithRed:197/255.0 green:0/255.0 blue:27/255.0 alpha:1.0];
    //return [UIColor whiteColor];
    return [UIColor colorWithRed:255/255.0 green:107/255.0 blue:108/255.0 alpha:1.0];
}

- (UIColor *)navbarBorderColor
{
    return [UIColor colorWithRed:229/255.0 green:230/255.0 blue:230/255.0 alpha:1.0];
}

+ (UIColor *)headerTextColor
{
    return [UIColor colorWithWhite:0.22 alpha:1.0];
}

+ (UIColor *) appBackgroundColor
{
    return [UIColor colorWithRed:249/255.0 green:248/255.0 blue:248/255.0 alpha:1.0];
}

@end
