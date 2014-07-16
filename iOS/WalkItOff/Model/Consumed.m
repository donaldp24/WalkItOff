//
//  Consumed.m
//  WalkItOff
//
//  Created by Donald Pae on 7/10/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "Consumed.h"
#import "ServerManager.h"
#import "CommonMethods.h"
#import "NSDate+walkitoff.h"
#import "Database+walkitoff.h"

@implementation Consumed

- (id)init
{
    self = [super init];
    if (self) {
        self.date = [NSDate date];
        self.stepsTaken = 0;
        self.caloriesConsumed = 0;
        self.milesWalked = 0;
    }
    return self;
}

+ (void)getConsumedWithLocal:(int)useruid withDate:(NSDate *)date success:(void(^)(Consumed *consumed))success failure:(void(^)(NSString *msg))failure
{
    [[Database sharedDatabase] getConsumed:useruid withDate:date success:success failure:failure];
}
//

+ (void)addConsumedWithLocal:(int)useruid withConsumed:(Consumed *)consumed success:(void(^)())success failure:(void(^)(NSString *msg))failure
{
    [[Database sharedDatabase] addConsumed:useruid withConsumed:consumed success:success failure:failure];
}


+ (void)getConsumedWithRemote:(int)useruid withDate:(NSDate *)date success:(void (^)(Consumed *))success failure:(void (^)(NSString *))failure
{
    NSMutableDictionary *params = [[NSMutableDictionary alloc] init];
    if (useruid >= 1)
        [params setObject:@(useruid) forKey:@"useruid"];
    [params setObject:date forKey:@"date"];
    
    DEF_SERVERMANAGER
    [manager postMethod:@"getConsumedWithDate" params:params handler:^(NSDictionary *response, NSError *error) {
        
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
        
        NSDictionary *obj = [response objectForKey:kResponseDataKey];
        Consumed *consumed = [[Consumed alloc] init];
        consumed.date = date;
        if (obj == nil)
        {
            success(consumed);
            return;
        }
        
        consumed.date = [CommonMethods str2date:[obj objectForKey:@"consumeddate"] withFormat:DATETIME_FORMAT];
        consumed.stepsTaken = [[obj objectForKey:@"stepstaken"] intValue];
        consumed.caloriesConsumed = [[obj objectForKey:@"caloriesconsumed"] floatValue];
        consumed.milesWalked = [[obj objectForKey:@"mileswalked"] floatValue];
        
        success(consumed);
    }];

}

+ (void)addConsumedWithRemote:(int)useruid withConsumed:(Consumed *)consumed success:(void (^)())success failure:(void (^)(NSString *))failure
{
    NSMutableDictionary *params = [[NSMutableDictionary alloc] init];
    if (useruid >= 1)
        [params setObject:@(useruid) forKey:@"useruid"];
    [params setObject:consumed.date forKey:@"consumeddate"];
    [params setObject:@(consumed.stepsTaken) forKey:@"stepstaken"];
    [params setObject:@(consumed.caloriesConsumed) forKey:@"caloriesconsumed"];
    [params setObject:@(consumed.milesWalked) forKey:@"mileswalked"];

    
    DEF_SERVERMANAGER
    [manager postMethod:@"addConsumed" params:params handler:^(NSDictionary *response, NSError *error) {
        
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
