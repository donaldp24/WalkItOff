//
//  CurrentFood.h
//  WalkItOff
//
//  Created by Donald Pae on 7/14/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "Food.h"

@interface CurrentFood : Food

@property (nonatomic, assign) int currentUid;
@property (nonatomic, assign) int isConsumed;
@property (nonatomic, strong) NSDate *consumedDate;


+ (void)getCurrentFoodsWithLocal:(int)useruid isConsumed:(BOOL)isConsumed success:(void (^)(NSMutableArray *))success failure:(void (^)(NSString *))failure;

+ (void)getCurrentFoodsWithLocal:(int)useruid withDate:(NSDate *)date success:(void (^)(NSMutableArray *))success failure:(void (^)(NSString *))failure;

+ (void)addFoodToCurrentWithLocal:(int)useruid food:(Food *)food success:(void (^)())success failure:(void (^)(NSString *))failure;
+ (void)removeFoodFromCurrentWithLocal:(int)useruid currentFood:(CurrentFood *)currentFood success:(void (^)())success failure:(void (^)(NSString *))failure;

+ (void)getCurrentFoodsWithRemote:(int)useruid isConsumed:(BOOL)isConsumed success:(void (^)(NSMutableArray *))success failure:(void (^)(NSString *))failure;

+ (void)getCurrentFoodsWithRemote:(int)useruid withDate:(NSDate *)date success:(void (^)(NSMutableArray *))success failure:(void (^)(NSString *))failure;


@end
