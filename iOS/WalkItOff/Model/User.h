//
//  User.h
//  WalkItOff
//
//  Created by Donald Pae on 7/2/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import <Foundation/Foundation.h>

typedef enum {
    UserTypeNormal = 0,
    UserTypeNoneAuth = 1,
    UserTypeFacebook = 2,
    UserTypeTwitter = 3
} UserType;

typedef enum {
    GenderMale = 0,
    GenderFemale = 1
}Gender;

@interface User : NSObject

@property (nonatomic) int uid;
@property (nonatomic, strong) NSString *name;
@property (nonatomic) int age;
@property (nonatomic) int gender;
@property (nonatomic) float weight;
@property (nonatomic) float height;
@property (nonatomic, strong) NSString *email;
@property (nonatomic, strong) NSString *pwd;
@property (nonatomic) UserType type;
@property (nonatomic, strong) NSString *token;

@property (nonatomic, strong) NSMutableArray *currentFoods;
@property (nonatomic, strong) NSMutableArray *favoritesFoods;


+ (User *)currentUser;
+ (void)setCurrentUser:(User *)user;

+ (void)registerUser:(User *)user success:(void(^)())success failure:(void(^)(NSString *msg))failrue;

+ (void)loginUserWithEmail:(NSString *)email pwd:(NSString *)pwd success:(void(^)(User *user))success failure:(void(^)(NSString *msg))failure;

+ (void)loginUserWithFacebook:(User *)user success:(void(^)(User *user))success failure:(void(^)(NSString *msg))failure;

+ (void)updateUser:(User *)user success:(void(^)(User *user))success failure:(void(^)(NSString *msg))failure;


@end
