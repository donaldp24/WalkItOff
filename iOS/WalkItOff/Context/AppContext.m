//
//  AppContext.m
//  WalkItOff
//
//  Created by Donald Pae on 6/30/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "AppContext.h"

static AppContext *_sharedContext = nil;

#define kContextPedometerStarted        @"pedometerStarted"
#define kContextNumberOfTodayStepsKey   @"numberOfTodaySteps"
#define kContextLastTimestampKey        @"lastTimestamp"
#define kContextIsLoggedInKey           @"isLoggedIn"
#define kContextStepsTakenKey           @"stepsTaken"
#define kContextResetDateKey            @"resetDate"

// temp values
#define kContextPercentageCaloriesBurnedKey @"percentageCaloriesBurned"
#define kContextCaloriesToBurnKey           @"caloriesToBurn"
#define kContextTotalCaloriesKey            @"totalCalories"


@implementation AppContext

+ (AppContext *)initContext:(int)useruid
{
     _sharedContext = [[AppContext alloc] initWithUserUid:useruid];
    return _sharedContext;
}

+ (AppContext *)sharedContext
{
    return _sharedContext;
}

- (id)initWithUserUid:(int)useruid {
    self = [super init];
    if (self)
    {
        self.useruid = useruid;
        [self load];
    }
    
    return self;
}

- (void)load
{
    NSDictionary *dicData = [[NSUserDefaults standardUserDefaults] objectForKey:[NSString stringWithFormat:@"appcontext_%d", self.useruid]];
    if (dicData == nil)
    {
        self.numberOfTodaySteps = 0;
        self.lastTimestamp = nil;
        self.pedometerStarted = NO;
        self.stepsTaken = 0;
        self.resetDate = [NSDate date];
        self.percentageCaloriesBurned = 0;
        self.caloriesToBurn = 0;
        self.totalCalories = 0;
    }
    else
    {
        // numberOfTodaySteps;
        NSObject *obj = [dicData objectForKey:kContextNumberOfTodayStepsKey];
        if (obj == nil || ![obj isKindOfClass:[NSNumber class]])
            self.numberOfTodaySteps = 0;
        else
            self.numberOfTodaySteps = [(NSNumber *)obj intValue];
        
        // lastTimestamp
        obj = [dicData objectForKey:kContextLastTimestampKey];
        if (obj == nil || ![obj isKindOfClass:[NSDate class]])
            self.lastTimestamp = nil;
        else
            self.lastTimestamp = (NSDate *)obj;
        
        // pedometerStarted
        obj = [dicData objectForKey:kContextPedometerStarted];
        if (obj == nil || ![obj isKindOfClass:[NSNumber class]])
            self.pedometerStarted = NO;
        else
            self.pedometerStarted = [(NSNumber *)obj boolValue];
        
        // stepsTaken
        obj = [dicData objectForKey:kContextStepsTakenKey];
        if (obj == nil || ![obj isKindOfClass:[NSNumber class]])
            self.stepsTaken = 0;
        else
            self.stepsTaken = [(NSNumber *)obj intValue];
        
        // resetDate
        obj = [dicData objectForKey:kContextResetDateKey];
        if (obj == nil || ![obj isKindOfClass:[NSDate class]])
            self.resetDate = [NSDate date];
        else
            self.resetDate = (NSDate *)obj;
        
        obj = [dicData objectForKey:kContextPercentageCaloriesBurnedKey];
        if (obj == nil || ![obj isKindOfClass:[NSNumber class]])
            self.percentageCaloriesBurned = 0;
        else
            self.percentageCaloriesBurned = [(NSNumber *)obj floatValue];
        
        obj = [dicData objectForKey:kContextCaloriesToBurnKey];
        if (obj == nil || ![obj isKindOfClass:[NSNumber class]])
            self.caloriesToBurn = 0;
        else
            self.caloriesToBurn = [(NSNumber *)obj floatValue];
        
        obj = [dicData objectForKey:kContextTotalCaloriesKey];
        if (obj == nil || ![obj isKindOfClass:[NSNumber class]])
            self.totalCalories = 0;
        else
            self.totalCalories = [(NSNumber *)obj floatValue];
    }
}

- (void)save
{


    NSMutableDictionary *dicData = [[NSMutableDictionary alloc] init];
    
    // numberOfTodaySteps;
    [dicData setObject:@(self.numberOfTodaySteps) forKey:kContextNumberOfTodayStepsKey];
    
    // lastTimestamp
    if (self.lastTimestamp != nil)
        [dicData setObject:self.lastTimestamp forKey:kContextLastTimestampKey];
    
    // pedometerStarted
    [dicData setObject:@(self.pedometerStarted) forKey:kContextPedometerStarted];
    
    // stepsTaken
    [dicData setObject:@(self.stepsTaken) forKey:kContextStepsTakenKey];
     
    // resetDate
    [dicData setObject:self.resetDate forKey:kContextResetDateKey];
    
    [dicData setObject:@(self.percentageCaloriesBurned) forKey:kContextPercentageCaloriesBurnedKey];
    
    [dicData setObject:@(self.caloriesToBurn) forKey:kContextCaloriesToBurnKey];
    
    [dicData setObject:@(self.totalCalories) forKey:kContextTotalCaloriesKey];
    
    [[NSUserDefaults standardUserDefaults] setObject:dicData forKey:[NSString stringWithFormat:@"appcontext_%d", self.useruid]];
    
    [[NSUserDefaults standardUserDefaults] synchronize];
}

@end
