//
//  Pedometer.m
//  WalkItOff
//
//  Created by Donald Pae on 6/7/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "Pedometer.h"
#import <CoreMotion/CoreMotion.h>

static Pedometer *_defaultPedometer = nil;

#define kThreshold  1.2
#define kAccelerometerUpdateInterval    0.1

@interface Pedometer() {
    double _prevVal;
    NSInteger _numberOfSteps;
}

@property (nonatomic, strong) CMStepCounter *cmStepCounter;
@property (nonatomic, strong) CMMotionManager *cmMotionManager;
@property (nonatomic, strong) NSOperationQueue *operationQueue;
@property (nonatomic) BOOL started;

@end

@implementation Pedometer

+ (Pedometer *)defaultPedometer
{
    if (_defaultPedometer == nil) {
        _defaultPedometer = [[Pedometer alloc] init];
        
        BOOL bAvailable = [CMStepCounter isStepCountingAvailable];
        bAvailable = [CMMotionActivityManager isActivityAvailable];
        bAvailable = bAvailable;
    }
    return _defaultPedometer;
}

- (id)init
{
    self = [super init];
    if (self) {
        //
    }
    return self;
}

- (BOOL) isStarted
{
    return self.started;
}

- (NSOperationQueue *)operationQueue
{
    if (_operationQueue == nil)
    {
        _operationQueue = [NSOperationQueue new];
    }
    return _operationQueue;
}

- (void)start
{
    if ([CMStepCounter isStepCountingAvailable])
    {
        self.cmStepCounter = [[CMStepCounter alloc] init];
        [self.cmStepCounter startStepCountingUpdatesToQueue:self.operationQueue updateOn:1 withHandler:^(NSInteger numberOfSteps, NSDate *timestamp, NSError *error)
         {
             [[NSOperationQueue mainQueue] addOperationWithBlock:^{
                 if (self.delegate)
                     [self.delegate updateStepCounter:numberOfSteps timestamp:timestamp];
             }];
         }];
    }
    else
    {
        _prevVal = 1;
        _numberOfSteps = 0;
        
        self.cmMotionManager = [[CMMotionManager alloc] init];
        [self.cmMotionManager setAccelerometerUpdateInterval:kAccelerometerUpdateInterval];
        [self.cmMotionManager startAccelerometerUpdatesToQueue:self.operationQueue withHandler:^(CMAccelerometerData *accelerometerData, NSError *error) {
            [[NSOperationQueue mainQueue] addOperationWithBlock:^{
                double currVal = sqrt(accelerometerData.acceleration.x * accelerometerData.acceleration.x + accelerometerData.acceleration.y * accelerometerData.acceleration.y + accelerometerData.acceleration.z * accelerometerData.acceleration.z);
                
                if (currVal >= kThreshold)
                {
                    if (currVal - _prevVal > 0)
                    {
                        _numberOfSteps ++;
                        NSDate *timestamp = [NSDate date];
                        if (self.delegate)
                            [self.delegate updateStepCounter:_numberOfSteps timestamp:timestamp];
                    }
                }
                _prevVal = currVal;
            }];
        }];
    }
    
    [[NSOperationQueue mainQueue] addOperationWithBlock:^{
        if (self.delegate)
            [self.delegate started];
    }];
}

- (void)stop
{
    if ([CMStepCounter isStepCountingAvailable])
    {
        [self.cmStepCounter stopStepCountingUpdates];
        
        // query step count
        NSDate *now = [NSDate date];
        NSCalendar *gregorian = [[NSCalendar alloc]
                                 initWithCalendarIdentifier:NSGregorianCalendar];
        NSDateComponents *comps = [gregorian components:NSCalendarUnitYear | NSCalendarUnitMonth | NSCalendarUnitDay | NSCalendarUnitHour fromDate:now];
        [comps setHour:0];
        NSDate *today = [gregorian dateFromComponents:comps];
        
        [self.cmStepCounter queryStepCountStartingFrom:today
                                                  to:now
                                             toQueue:[NSOperationQueue mainQueue]
                                         withHandler:^(NSInteger numberOfSteps, NSError *error) {
                                             NSLog(@"%s %ld %@", __PRETTY_FUNCTION__, (long)numberOfSteps, error);
                                             /*
                                             [weakSelf fadeAnimationVisible:YES];
                                             weakSelf.totalStepsLabel.text = [@(numberOfSteps) stringValue];
                                              */
                                         }];
    }
    else
    {
        [self.cmMotionManager stopAccelerometerUpdates];
    }
    
    [[NSOperationQueue mainQueue] addOperationWithBlock:^{
        if (self.delegate)
            [self.delegate stopped];
    }];
}

+ (void)getNumberOfTodaySteps:(void (^)(NSInteger))handler
{
    @autoreleasepool {
        
    
    CMStepCounter *stepCount = [[CMStepCounter alloc] init];
    
    // query step count
    NSDate *now = [NSDate date];
    NSCalendar *gregorian = [[NSCalendar alloc]
                             initWithCalendarIdentifier:NSGregorianCalendar];
    NSDateComponents *comps = [gregorian components:NSCalendarUnitYear | NSCalendarUnitMonth | NSCalendarUnitDay | NSCalendarUnitHour fromDate:now];
    [comps setHour:0];
    NSDate *today = [gregorian dateFromComponents:comps];
    
    [stepCount queryStepCountStartingFrom:today
                                                to:now
                                           toQueue:[NSOperationQueue mainQueue]
                                       withHandler:^(NSInteger numberOfSteps, NSError *error) {
                                           NSLog(@"%s %ld %@", __PRETTY_FUNCTION__, (long)numberOfSteps, error);
                                           /*
                                            [weakSelf fadeAnimationVisible:YES];
                                            weakSelf.totalStepsLabel.text = [@(numberOfSteps) stringValue];
                                            */
                                       }];
    }
}

@end
