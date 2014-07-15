//
//  UserContext.h
//  WalkItOff
//
//  Created by Donald Pae on 7/14/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "User.h"

@interface UserContext : NSObject

@property (nonatomic) BOOL isLoggedIn;

+ (UserContext *)sharedContext;

+ (void)saveUser:(User *)user;
+ (User *)loadUser;

+ (void)setDefaultLogin;
+ (void)clearDefaultLogin;
+ (BOOL)getDefaultLogin;

@end
