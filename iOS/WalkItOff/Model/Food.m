//
//  Food.m
//  WalkItOff
//
//  Created by Donald Pae on 6/13/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "Food.h"
#import "ServerManager.h"
#import "Database+walkitoff.h"

@implementation Food

+ (void)getFoodsWithLocal:(int)useruid keyword:(NSString *)keyword page:(int)page success:(void (^)(NSMutableArray *))success failure:(void (^)(NSString *))failure
{
    [[Database sharedDatabase] getCustomFoods:useruid keyword:keyword success:success failure:failure];
}

+ (void)getFoodsWithRemote:(int)useruid keyword:(NSString *)keyword page:(int)page success:(void (^)(NSMutableArray *, BOOL))success failure:(void (^)(NSString *))failure
{
    NSMutableDictionary *params = [[NSMutableDictionary alloc] init];
    if (useruid >= 1)
        [params setObject:@(useruid) forKey:@"useruid"];
    if (keyword != nil && keyword.length > 0)
        [params setObject:keyword forKey:@"keyword"];
    
    [params setObject:@(page) forKey:@"page"];
    
    DEF_SERVERMANAGER
    [manager postMethod:@"getFoodsWithPage" params:params handler:^(NSDictionary *response, NSError *error) {
        
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
        
        NSDictionary *dataDic = [response objectForKey:kResponseDataKey];
        BOOL hasNext = NO;
        NSObject *objNext = [dataDic objectForKey:@"hasNext"];
        if (objNext != nil && [objNext isKindOfClass:[NSString class]])
        {
            if ([(NSString *)objNext isEqualToString:@"true"])
                hasNext = YES;
            else
                hasNext = NO;
        }
        
        NSArray *arrayData = [dataDic objectForKey:@"array"];
        NSMutableArray *ret = [[NSMutableArray alloc] init];
        for (NSDictionary *itemDic in arrayData) {
            
            NSObject *objUid = [itemDic objectForKey:@"uid"];
            if (objUid == nil)
                continue;
            
            NSObject *objName = [itemDic objectForKey:@"name"];
            if (objName == nil)
                continue;
            
            NSObject *objCalories = [itemDic objectForKey:@"calories"];
            if (objCalories == nil)
                continue;
            
            NSObject *objBrand = [itemDic objectForKey:@"brand"];
            NSObject *objServingSize = [itemDic objectForKey:@"servingsize"];
            NSObject *objProtein = [itemDic objectForKey:@"protein"];
            NSObject *objCarbs = [itemDic objectForKey:@"carbs"];
            NSObject *objFat = [itemDic objectForKey:@"fat"];
            NSObject *objImage = [itemDic objectForKey:@"image"];
            NSObject *objUseruid = [itemDic objectForKey:@"useruid"];
            
            
            Food *food = [[Food alloc] init];
            food.uid = [(NSString *)objUid intValue];
            food.calories = [(NSString *)objCalories floatValue];
            food.name = [NSString stringWithFormat:@"%@", (NSString *)objName];
            food.brand = [NSString stringWithFormat:@"%@", (NSString *)objBrand];
            food.servingsize = [NSString stringWithFormat:@"%@", (NSString *)objServingSize];
            food.protein = [NSString stringWithFormat:@"%@", (NSString *)objProtein];
            food.carbs = [NSString stringWithFormat:@"%@", (NSString *)objCarbs];
            food.fat = [NSString stringWithFormat:@"%@", (NSString *)objFat];
            food.image = [NSString stringWithFormat:@"%@", (NSString *)objImage];
            food.useruid = [(NSString*)objUseruid intValue];
            
            food.isCustom = 0;
            
            [ret addObject:food];
        }
        
        success(ret, hasNext);
    }];
}

+ (void)getFavoritesFoodsWithLocal:(int)useruid success:(void (^)(NSMutableArray *))success failure:(void (^)(NSString *))failure
{
    [[Database sharedDatabase] getFavoritesFoods:useruid success:success failure:failure];
}

+ (void)addFoodToFavoritesWithLocal:(int)useruid food:(Food *)food success:(void (^)())success failure:(void (^)(NSString *))failure
{
    [[Database sharedDatabase] addFoodToFavorites:useruid food:food success:success failure:failure];
}

+ (void)removeFoodFromFavoritesWithLocal:(int)useruid food:(Food *)food success:(void (^)())success failure:(void (^)(NSString *))failure
{
    [[Database sharedDatabase] removeFoodFromFavorites:useruid food:food success:success failure:failure];
}

+ (void)getFavoritesFoodsWithRemote:(int)useruid success:(void (^)(NSMutableArray *))success failure:(void (^)(NSString *))failure
{
    NSMutableDictionary *params = [[NSMutableDictionary alloc] init];
    if (useruid >= 1)
        [params setObject:@(useruid) forKey:@"useruid"];
    
    DEF_SERVERMANAGER
    [manager postMethod:@"getFavoritesFood" params:params handler:^(NSDictionary *response, NSError *error) {
        
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


+ (void)addFoodToCurrentWithRemote:(int)uid food:(Food *)food success:(void (^)())success failure:(void (^)(NSString *))failure
{
    NSMutableDictionary *params = [[NSMutableDictionary alloc] init];
    if (uid >= 1)
        [params setObject:@(uid) forKey:@"useruid"];
    [params setObject:@(food.uid) forKey:@"fooduid"];
    
    DEF_SERVERMANAGER
    [manager postMethod:@"addFoodToCurrent" params:params handler:^(NSDictionary *response, NSError *error) {
        
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
        
        success();
    }];
}


+ (void)addFoodToFavoritesWithRemote:(int)uid food:(Food *)food success:(void (^)())success failure:(void (^)(NSString *))failure
{
    NSMutableDictionary *params = [[NSMutableDictionary alloc] init];
    if (uid >= 1)
        [params setObject:@(uid) forKey:@"useruid"];
    [params setObject:@(food.uid) forKey:@"fooduid"];
    
    DEF_SERVERMANAGER
    [manager postMethod:@"addFoodToFavorites" params:params handler:^(NSDictionary *response, NSError *error) {
        
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
        
        success();
    }];
}

+ (void)removeFoodFromCurrentWithRemote:(int)useruid food:(Food *)food success:(void (^)())success failure:(void (^)(NSString *))failure
{
    NSMutableDictionary *params = [[NSMutableDictionary alloc] init];
    if (useruid >= 1)
        [params setObject:@(useruid) forKey:@"useruid"];
    [params setObject:@(food.uid) forKey:@"fooduid"];
    
    DEF_SERVERMANAGER
    [manager postMethod:@"removeFoodFromCurrent" params:params handler:^(NSDictionary *response, NSError *error) {
        
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
        
        success();
    }];
}

+ (void)removeFoodFromFavoritesWithRemote:(int)useruid food:(Food *)food success:(void (^)())success failure:(void (^)(NSString *))failure
{
    NSMutableDictionary *params = [[NSMutableDictionary alloc] init];
    if (useruid >= 1)
        [params setObject:@(useruid) forKey:@"useruid"];
    [params setObject:@(food.uid) forKey:@"fooduid"];
    
    DEF_SERVERMANAGER
    [manager postMethod:@"removeFoodFromFavorites" params:params handler:^(NSDictionary *response, NSError *error) {
        
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
        
        success();
    }];
}

+ (void)consumedFoodsWithLocal:(int)useruid arrayData:(NSMutableArray *)arrayData success:(void (^)())success failure:(void (^)(NSString *))failure
{
    [[Database sharedDatabase] consumedFoods:useruid foods:arrayData];
}

+ (void)addCustomFoodWithLocal:(int)useruid food:(Food *)food success:(void (^)())success failure:(void (^)(NSString *))failure
{
    [[Database sharedDatabase] addCustomFood:useruid food:food success:success failure:failure];
}

+ (void)addCustomFoodWithLocal:(int)useruid name:(NSString *)name calories:(CGFloat)calories success:(void (^)())success failure:(void (^)(NSString *))failure
{
    Food *food = [[Food alloc] init];
    food.name = name;
    food.calories = calories;
    [Food addCustomFoodWithLocal:useruid food:food success:success failure:failure];
}

+ (void)consumedFoodsWithRemote:(int)useruid arrayData:(NSMutableArray *)arrayData success:(void (^)())success failure:(void (^)(NSString *))failure
{
    NSMutableDictionary *params = [[NSMutableDictionary alloc] init];
    if (useruid >= 1)
        [params setObject:@(useruid) forKey:@"useruid"];
    NSMutableArray *dicArray = [[NSMutableArray alloc] init];
    for (Food * food in arrayData) {
        NSMutableDictionary *dicData = [[NSMutableDictionary alloc] init];
        [dicData setObject:@(food.uid) forKey:@"uid"];
        [dicArray addObject:dicData];
    }
    
    NSData *infoData = [NSJSONSerialization dataWithJSONObject:dicArray options:0 error:nil];
    NSString *infoStr = [[NSString alloc] initWithBytes:[infoData bytes] length:[infoData length] encoding:NSUTF8StringEncoding];
    
    [params setObject:infoStr forKey:@"fooduids"];
    
    DEF_SERVERMANAGER
    [manager postMethod:@"consumedFoods" params:params handler:^(NSDictionary *response, NSError *error) {
        
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
        
        success();
    }];
}

+ (void)addCustomFoodWithRemote:(int)useruid food:(Food *)food success:(void (^)())success failure:(void (^)(NSString *))failure
{
    NSMutableDictionary *params = [[NSMutableDictionary alloc] init];
    if (useruid >= 1)
        [params setObject:@(useruid) forKey:@"useruid"];
    [params setObject:food.name forKey:@"name"];
    [params setObject:@(food.calories) forKey:@"calories"];
    
    
    
    DEF_SERVERMANAGER
    [manager postMethod:@"addCustomFood" params:params handler:^(NSDictionary *response, NSError *error) {
        
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
        
        success();
    }];
}


@end
