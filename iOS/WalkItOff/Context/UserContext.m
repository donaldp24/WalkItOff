//
//  UserContext.m
//  WalkItOff
//
//  Created by Donald Pae on 7/14/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "UserContext.h"

static UserContext *_sharedUserContext = nil;

@implementation UserContext

+ (UserContext *)sharedContext
{
    if (_sharedUserContext == nil)
    {
        _sharedUserContext = [[UserContext alloc] init];
    }
    return _sharedUserContext;
}

- (id)init
{
    self = [super init];
    self.isLoggedIn = NO;
    return self;
}

+ (void)saveUser:(User *)user
{
    NSMutableDictionary *dicUser = [[NSMutableDictionary alloc] init];
    [dicUser setObject:@(user.uid) forKey:@"uid"];
    [dicUser setObject:user.name forKey:@"name"];
    [dicUser setObject:@(user.age) forKey:@"age"];
    [dicUser setObject:@(user.gender) forKey:@"gender"];
    [dicUser setObject:@(user.weight) forKey:@"weight"];
    [dicUser setObject:@(user.height) forKey:@"height"];
    [dicUser setObject:user.email forKey:@"email"];
    [dicUser setObject:user.pwd forKey:@"pwd"];
    [dicUser setObject:@(user.type) forKey:@"type"];
    [dicUser setObject:user.token forKey:@"token"];

    [[NSUserDefaults standardUserDefaults] setObject:dicUser forKey:@"currentUser"];
    [[NSUserDefaults standardUserDefaults] synchronize];
}

+ (User *)loadUser
{
    User *user = [[User alloc] init];
    NSDictionary *dicUser = [[NSUserDefaults standardUserDefaults] objectForKey:@"currentUser"];
    if (dicUser == nil)
        return nil;
    
    user.uid = [[dicUser objectForKey:@"uid"] intValue];
    user.name = [dicUser objectForKey:@"name"];
    user.age = [[dicUser objectForKey:@"age"] intValue];
    user.gender = [[dicUser objectForKey:@"gender"] intValue];
    user.weight = [[dicUser objectForKey:@"weight"] floatValue];
    user.height = [[dicUser objectForKey:@"height"] floatValue];
    user.email = [dicUser objectForKey:@"email"];
    user.pwd = [dicUser objectForKey:@"pwd"];
    user.type = [[dicUser objectForKey:@"type"] intValue];
    user.token = [dicUser objectForKey:@"token"];
    return user;
}

+ (void)setDefaultLogin
{
    [[NSUserDefaults standardUserDefaults] setObject:@(YES) forKey:@"defaultlogin"];
    [[NSUserDefaults standardUserDefaults] synchronize];
}

+ (void)clearDefaultLogin
{
    [[NSUserDefaults standardUserDefaults] setObject:@(NO) forKey:@"defaultlogin"];
    [[NSUserDefaults standardUserDefaults] synchronize];
}

+ (BOOL)getDefaultLogin
{
    NSObject *obj = [[NSUserDefaults standardUserDefaults] objectForKey:@"defaultlogin"];
    if (obj == nil)
        return NO;
    return [(NSNumber *)obj boolValue];
}



@end
