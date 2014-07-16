//
//  Food.h
//  WalkItOff
//
//  Created by Donald Pae on 6/13/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface Food : NSObject

@property (nonatomic, assign) int uid;
@property (nonatomic, strong) NSString *brand;
@property (nonatomic, strong) NSString *name;
@property (nonatomic, strong) NSString *servingsize;
@property (nonatomic, assign) CGFloat calories;
@property (nonatomic, strong) NSString *protein;
@property (nonatomic, strong) NSString *carbs;
@property (nonatomic, strong) NSString *fat;
@property (nonatomic, strong) NSString *image;
@property (nonatomic, assign) int useruid;
@property (nonatomic, assign) int isCustom;

+ (void)getFoodsWithLocal:(int)useruid keyword:(NSString *)keyword page:(int)page success:(void(^)(NSMutableArray *foods))success failure:(void(^)(NSString *msg))failure;

+ (void)getFoodsWithRemote:(int)useruid keyword:(NSString *)keyword page:(int)page success:(void(^)(NSMutableArray *foods, BOOL hasNext))success failure:(void(^)(NSString *msg))failure;


+ (void)getFavoritesFoodsWithLocal:(int)useruid success:(void (^)(NSMutableArray *))success failure:(void (^)(NSString *))failure;
+ (void)addFoodToFavoritesWithLocal:(int)useruid food:(Food *)food success:(void(^)())success failure:(void (^)(NSString *msg))failure;
+ (void)removeFoodFromFavoritesWithLocal:(int)useruid food:(Food *)food success:(void(^)())success failure:(void (^)(NSString *msg))failure;

+ (void)getFavoritesFoodsWithRemote:(int)useruid success:(void (^)(NSMutableArray *))success failure:(void (^)(NSString *))failure;
+ (void)addFoodToFavoritesWithRemote:(int)useruid food:(Food *)food success:(void(^)())success failure:(void (^)(NSString *msg))failure;
+ (void)removeFoodFromFavoritesWithRemote:(int)useruid food:(Food *)food success:(void(^)())success failure:(void (^)(NSString *msg))failure;

+ (void)consumedFoodsWithLocal:(int)useruid arrayData:(NSMutableArray *)arrayData success:(void(^)())success failure:(void (^)(NSString *msg))failure;
+ (void)addCustomFoodWithLocal:(int)useruid food:(Food *)food success:(void (^)())success failure:(void (^)(NSString *))failure;
+ (void)addCustomFoodWithLocal:(int)useruid name:(NSString *)name calories:(CGFloat)calories success:(void (^)())success failure:(void (^)(NSString *))failure;

+ (void)consumedFoodsWithRemote:(int)useruid arrayData:(NSMutableArray *)arrayData success:(void(^)())success failure:(void (^)(NSString *msg))failure;
+ (void)addCustomFoodWithRemote:(int)useruid food:(Food *)food success:(void (^)())success failure:(void (^)(NSString *))failure;

@end
