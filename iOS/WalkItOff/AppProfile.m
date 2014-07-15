//
//  AppProfile.m
//  WalkItOff
//
//  Created by Donald Pae on 6/8/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "AppProfile.h"

static AppProfile *_sharedProfile = nil;

@interface AppProfile() {
}

@property(nonatomic, strong) NSUserDefaults *userDefaults;

@end

@implementation AppProfile

+ (AppProfile *)sharedProfile
{
    if (_sharedProfile == nil)
    {
        _sharedProfile = [[AppProfile alloc] init];
    }
    return _sharedProfile;
}

- (id)init
{
    self = [super init];
    self.userDefaults = [NSUserDefaults standardUserDefaults];
    return self;
}


@end
