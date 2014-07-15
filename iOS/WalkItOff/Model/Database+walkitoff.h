//
//  Database+walkitoff.h
//  WalkItOff
//
//  Created by Donald Pae on 7/3/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "FMDatabase.h"
#import "Consumed.h"
#import "Food.h"
#import "User.h"
#import "CurrentFood.h"
#import "Consumed.h"

#define DATABASE_FILENAME   @"walkitoff.db"
#define DATABASE_VERSION    @"1"


@interface Database : NSObject {
    
    FMDatabase *_fmdb;
    
}


+ (Database *)sharedDatabase;

// custom foods
- (void)addCustomFood:(int)useruid food:(Food *)food success:(void(^)())success failure:(void(^)(NSString *msg))failure;
- (void)removeCustomFood:(int)useruid food:(Food *)food success:(void(^)())success failure:(void(^)(NSString *msg))failure;
- (void)getCustomFoods:(int)useruid keyword:(NSString *)keyword success:(void(^)(NSMutableArray *foods))success failure:(void(^)(NSString *msg))failure;

// current
- (void)addFoodToCurrent:(int)useruid food:(Food *)food success:(void(^)())success failure:(void(^)(NSString *msg))failure;
- (void)removeFoodFromCurrent:(int)useruid currentFood:(CurrentFood *)food success:(void(^)())success failure:(void(^)(NSString *msg))failure;
- (void)getCurrentFoods:(int)useruid success:(void (^)(NSMutableArray *foods))success failure:(void (^)(NSString *))failure;
- (void)getCurrentFoods:(int)useruid withDate:(NSDate *)date success:(void (^)(NSMutableArray *foods))success failure:(void (^)(NSString *))failure;

- (void)consumedFoods:(int)useruid foods:(NSMutableArray *)foods;


// favorite
- (void)addFoodToFavorites:(int)useruid food:(Food *)food success:(void(^)())success failure:(void(^)(NSString *msg))failure;
- (void)removeFoodFromFavorites:(int)useruid food:(Food *)food success:(void(^)())success failure:(void(^)(NSString *msg))failure;
- (void)getFavoritesFoods:(int)useruid success:(void (^)(NSMutableArray *foods))success failure:(void (^)(NSString *))failure;

// consumed
- (void)addConsumed:(int)useruid withConsumed:(Consumed *)consumed success:(void(^)())success failure:(void(^)(NSString *msg))failure;
- (void)getConsumed:(int)useruid withDate:(NSDate *)date success:(void(^)(Consumed *consumed))success failure:(void(^)(NSString *msg))failure;




@end
