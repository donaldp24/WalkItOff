//
//  User.m
//  WalkItOff
//
//  Created by Donald Pae on 7/2/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "User.h"
#import "ServerManager.h"

static User *_currentUser = nil;;

@implementation User

+ (User *)currentUser
{
    return _currentUser;
}

+ (void)setCurrentUser:(User *)user
{
    _currentUser = user;
}

- (id)init
{
    self = [super init];
    if (self) {
        self.uid = 0;
        self.name = @"";
        self.age = 30;
        self.gender = 0;
        self.type = UserTypeNoneAuth;
        self.weight = 75;
        self.height = 175;
        self.email = @"";
        self.pwd = @"";
        self.token = @"";
        
        self.currentFoods = [[NSMutableArray alloc] init];
        self.favoritesFoods = [[NSMutableArray alloc] init];
    }
    return self;
}

+ (void)registerUser:(User *)user success:(void (^)())success failure:(void (^)(NSString *))failure
{
    NSDictionary *params = @{@"name":user.name,
                             @"age":@(user.age),
                             @"gender":@(user.gender),
                             @"weight":@(user.weight),
                             @"height":@(user.height),
                             @"email":user.email,
                             @"pwd":user.pwd,
                             @"type":@(user.type),
                             @"token":user.token
                             };
    DEF_SERVERMANAGER
    [manager postMethod:@"registerUser" params:params handler:^(NSDictionary *response, NSError *error) {
        
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

+ (User *)getUserFromResponse:(NSDictionary *)data
{
    User *user = [[User alloc] init];
    user.uid = [[data objectForKey:@"uid"] intValue];
    user.name = [data objectForKey:@"name"];
    user.age = [[data objectForKey:@"age"] intValue];
    user.gender = [[data objectForKey:@"gender"] intValue];
    user.weight = [[data objectForKey:@"weight"] floatValue];
    user.height = [[data objectForKey:@"height"] floatValue];
    user.email = [data objectForKey:@"email"];
    user.pwd = [data objectForKey:@"pwd"];
    user.type = [[data objectForKey:@"type"] intValue];
    user.token = [data objectForKey:@"token"];
    return user;
}

+ (void)loginUserWithEmail:(NSString *)email pwd:(NSString *)pwd success:(void (^)(User *))success failure:(void (^)(NSString *))failure
{
    NSDictionary *params = @{@"email":email,
                             @"pwd":pwd};
    DEF_SERVERMANAGER
    [manager postMethod:@"loginUserWithEmail" params:params handler:^(NSDictionary *response, NSError *error){
        
        if (error != nil)
        {
            failure([error localizedDescription]);
            return;
        }
        
        int errorCode = [[response objectForKey:kResponseErrorKey] intValue];
        if (errorCode != ServiceSuccess)
        {
            NSString *msg = [response objectForKey:kResponseMsgKey];
            failure(msg);
            return;
        }
        
        NSDictionary *data = [response objectForKey:kResponseDataKey];
        
        User *user = [User getUserFromResponse:data];
        user.type = UserTypeNormal;
        
        [User setCurrentUser:user];
        
        success(user);
        
    }];
}

+ (void)loginUserWithFacebook:(User *)user success:(void (^)(User *))success failure:(void (^)(NSString *))failure
{
    NSDictionary *params = @{@"email":user.email,
                             @"name":user.name,
                             @"gender":@(user.gender),
                             @"age":@(user.age),
                             @"token":user.token};
    DEF_SERVERMANAGER
    [manager postMethod:@"loginUserWithFacebook" params:params handler:^(NSDictionary *response, NSError *error){
        
        if (error != nil)
        {
            failure([error localizedDescription]);
            return;
        }
        
        int errorCode = [[response objectForKey:kResponseErrorKey] intValue];
        if (errorCode != ServiceSuccess)
        {
            NSString *msg = [response objectForKey:kResponseMsgKey];
            failure(msg);
            return;
        }
        
        NSDictionary *data = [response objectForKey:kResponseDataKey];
        
        User *user = [User getUserFromResponse:data];
        user.type = UserTypeFacebook;
        
        [User setCurrentUser:user];
        
        success(user);
        
    }];
}

+ (void)updateUser:(User *)user success:(void (^)(User *))success failure:(void (^)(NSString *))failure
{
    NSDictionary *params = @{@"uid":@(user.uid),
                             @"name":user.name,
                             @"age":@(user.age),
                             @"gender":@(user.gender),
                             @"weight":@(user.weight),
                             @"height":@(user.height),
                             @"email":user.email,
                             @"pwd":user.pwd,
                             @"type":@(user.type),
                             @"token":user.token
                             };
    DEF_SERVERMANAGER
    [manager postMethod:@"updateUser" params:params handler:^(NSDictionary *response, NSError *error) {
        
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
        
        NSDictionary *data = [response objectForKey:kResponseDataKey];
        
        User *user = [User getUserFromResponse:data];
        
        success(user);
        
    }];
}

@end
