//
//  Pedometer.h
//  WalkItOff
//
//  Created by Donald Pae on 6/7/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <CoreMotion/CoreMotion.h>

@protocol PedometerDelegate <NSObject>

@required
- (void) started;
- (void) stopped;
- (void) updateStepCounter:(NSInteger)numberOfSteps timestamp:(NSDate *)timestamp;

@end

@interface Pedometer : NSObject {
    
}

@property (nonatomic, strong) id<PedometerDelegate> delegate;

+ (Pedometer *)defaultPedometer;

- (BOOL)isStarted;

- (void)start;
- (void)stop;

+ (void) getNumberOfTodaySteps:(void(^)(NSInteger numberOfSteps))handler;


@end
