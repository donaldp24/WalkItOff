//
//  UIManager.h
//  WalkItOff
//
//  Created by Donald Pae on 6/11/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface UIManager : NSObject


+ (UIManager *)sharedUIManager;

- (NSString *)appTitle;

- (NSInteger)navbarStyle;
- (UIColor *)navbarTintColor;
- (NSDictionary *)navbarTitleTextAttributes;
- (UIColor *)navbarBarTintColor;
- (UIColor *)navbarBorderColor;


+ (UIColor *)headerTextColor;
+ (UIColor *) appBackgroundColor;



@end
