//
//  AppDelegate.h
//  WalkItOff
//
//  Created by Donald Pae on 6/7/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import <UIKit/UIKit.h>

#import "Pedometer.h"

@protocol PedometerViewerDelegate <NSObject>

@required
- (void)updateNumberOfSteps:(NSInteger)numberOfSteps;
- (void)consumedCurrentFoods:(NSInteger)stepsTaken withDate:(NSDate *)date;

@end

@interface AppDelegate : UIResponder <UIApplicationDelegate, UITabBarControllerDelegate, PedometerDelegate>

@property (strong, nonatomic) UIWindow *window;
@property (strong, nonatomic) UITabBarController *tabBarController;
@property (strong, nonatomic) Pedometer *pedometer;
@property (strong, nonatomic) id<PedometerViewerDelegate> pedometerViewerDelegate;
@property (strong, nonatomic) ACAccountStore *accountStore;

@end
