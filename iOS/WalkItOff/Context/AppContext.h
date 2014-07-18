//
//  AppContext.h
//  WalkItOff
//
//  Created by Donald Pae on 6/30/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import <Foundation/Foundation.h>

#define POST_CALORIES_MILESTONE         500


@interface AppContext : NSObject

@property (nonatomic) int useruid;

@property (nonatomic) BOOL pedometerStarted;
@property (nonatomic) NSInteger numberOfTodaySteps;
@property (nonatomic, strong) NSDate *lastTimestamp;
@property (nonatomic, strong) NSDate *resetDate;
@property (nonatomic) NSInteger stepsTaken;
@property (nonatomic) CGFloat nextPostCalories;

@property (nonatomic) CGFloat percentageCaloriesBurned;
@property (nonatomic) CGFloat caloriesToBurn;
@property (nonatomic) CGFloat totalCalories;


+ (AppContext *)initContext:(int)useruid;
+ (AppContext *)sharedContext;

- (void)save;

@end
