//
//  CurrentFood.m
//  WalkItOff
//
//  Created by Donald Pae on 7/14/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "CurrentFood.h"
#import "ServerManager.h"
#import "Database+walkitoff.h"

@implementation CurrentFood


+ (void)getCurrentFoodsWithLocal:(int)useruid isConsumed:(BOOL)isConsumed success:(void (^)(NSMutableArray *))success failure:(void (^)(NSString *))failure
{
    [[Database sharedDatabase] getCurrentFoods:useruid success:success failure:failure];
}

+ (void)getCurrentFoodsWithLocal:(int)useruid withDate:(NSDate *)date success:(void (^)(NSMutableArray *))success failure:(void (^)(NSString *))failure
{
    [[Database sharedDatabase] getCurrentFoods:useruid withDate:date success:success failure:failure];
}

+ (void)addFoodToCurrentWithLocal:(int)useruid food:(Food *)food success:(void (^)())success failure:(void (^)(NSString *))failure
{
    [[Database sharedDatabase] addFoodToCurrent:useruid food:food success:success failure:failure];
}

+ (void)removeFoodFromCurrentWithLocal:(int)useruid currentFood:(CurrentFood *)currentFood success:(void (^)())success failure:(void (^)(NSString *))failure
{
    [[Database sharedDatabase] removeFoodFromCurrent:useruid currentFood:currentFood success:success failure:failure];
}

+ (void)getCurrentFoodsWithRemote:(int)useruid isConsumed:(BOOL)isConsumed success:(void (^)(NSMutableArray *))success failure:(void (^)(NSString *))failure
{
    NSMutableDictionary *params = [[NSMutableDictionary alloc] init];
    if (useruid >= 1)
        [params setObject:@(useruid) forKey:@"useruid"];
    [params setObject:@(isConsumed) forKey:@"isconsumed"];
    
    DEF_SERVERMANAGER
    [manager postMethod:@"getCurrentFoods" params:params handler:^(NSDictionary *response, NSError *error) {
        
        if (error != nil)
        {
            failure([error localizedDescription]);
            return;
        }
        
        NSString *strErrorCode = [response objectForKey:kResponseErrorKey];
        int errorCode = [strErrorCode intValue];
        if (strErrorCode == nil || strErrorCode.length == 0)
        {
            NSString *msg = @"Unknown error";
            failure(msg);
            return;
        }
        
        if (errorCode != ServiceSuccess)
        {
            NSString *msg = [response objectForKey:kResponseMsgKey];
            failure(msg);
            return;
        }
        
        
        NSArray *arrayData = [response objectForKey:kResponseDataKey];
        NSMutableArray *ret = [[NSMutableArray alloc] init];
        for (NSDictionary *itemDic in arrayData) {
            
            NSObject *objUid = [itemDic objectForKey:@"fooduid"];
            if (objUid == nil)
                continue;
            
            NSObject *objName = [itemDic objectForKey:@"name"];
            if (objName == nil)
                continue;
            
            NSObject *objCalories = [itemDic objectForKey:@"calories"];
            if (objCalories == nil)
                continue;
            
            Food *food = [[Food alloc] init];
            food.uid = [(NSString *)objUid intValue];
            food.calories = [(NSString *)objCalories floatValue];
            food.name = [NSString stringWithFormat:@"%@", (NSString *)objName];
            
            [ret addObject:food];
        }
        
        success(ret);
    }];
}

+ (void)getCurrentFoodsWithRemote:(int)useruid withDate:(NSDate *)date success:(void (^)(NSMutableArray *))success failure:(void (^)(NSString *))failure
{
    NSMutableDictionary *params = [[NSMutableDictionary alloc] init];
    if (useruid >= 1)
        [params setObject:@(useruid) forKey:@"useruid"];
    [params setObject:date forKey:@"date"];
    
    DEF_SERVERMANAGER
    [manager postMethod:@"getCurrentFoodsWithDate" params:params handler:^(NSDictionary *response, NSError *error) {
        
        if (error != nil)
        {
            failure([error localizedDescription]);
            return;
        }
        
        NSString *strErrorCode = [response objectForKey:kResponseErrorKey];
        int errorCode = [strErrorCode intValue];
        if (strErrorCode == nil || strErrorCode.length == 0)
        {
            NSString *msg = @"Unknown error";
            failure(msg);
            return;
        }
        
        if (errorCode != ServiceSuccess)
        {
            NSString *msg = [response objectForKey:kResponseMsgKey];
            failure(msg);
            return;
        }
        
        
        NSArray *arrayData = [response objectForKey:kResponseDataKey];
        NSMutableArray *ret = [[NSMutableArray alloc] init];
        for (NSDictionary *itemDic in arrayData) {
            
            NSObject *objUid = [itemDic objectForKey:@"fooduid"];
            if (objUid == nil)
                continue;
            
            NSObject *objName = [itemDic objectForKey:@"name"];
            if (objName == nil)
                continue;
            
            NSObject *objCalories = [itemDic objectForKey:@"calories"];
            if (objCalories == nil)
                continue;
            
            Food *food = [[Food alloc] init];
            food.uid = [(NSString *)objUid intValue];
            food.calories = [(NSString *)objCalories floatValue];
            food.name = [NSString stringWithFormat:@"%@", (NSString *)objName];
            
            [ret addObject:food];
        }
        
        success(ret);
    }];
}


@end
