//
//  Consumed.h
//  WalkItOff
//
//  Created by Donald Pae on 7/10/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface Consumed : NSObject

@property (nonatomic, strong) NSDate *date;
@property (nonatomic) int stepsTaken;
@property (nonatomic) CGFloat caloriesConsumed;
@property (nonatomic) CGFloat milesWalked;

+ (void)getConsumedWithLocal:(int)useruid withDate:(NSDate *)date success:(void(^)(Consumed *consumed))success failure:(void(^)(NSString *msg))failure;
+ (void)addConsumedWithLocal:(int)useruid withConsumed:(Consumed *)consumed success:(void(^)())success failure:(void(^)(NSString *msg))failure;

+ (void)getConsumedWithRemote:(int)useruid withDate:(NSDate *)date success:(void(^)(Consumed *consumed))success failure:(void(^)(NSString *msg))failure;
+ (void)addConsumedWithRemote:(int)useruid withConsumed:(Consumed *)consumed success:(void(^)())consumed failure:(void(^)(NSString *msg))failure;

@end
