//
//  AppSettings.m
//  WalkItOff
//
//  Created by Donald Pae on 7/13/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "AppSettings.h"

#define kAppSettingsTwitterUserKey   @"twitteruser"
#define kAppSettingsTwitterPwdKey    @"twitterpwd"
#define kAppSettingsTwitterTweet1Key @"tweet1"
#define kAppSettingsTwitterTweet2Key @"tweet2"

#define kAppSettingsFbUserKey       @"fbuser"
#define kAppSettingsFbPwdKey        @"fbpwd"
#define kAppSettingsFbPost1Key      @"post1"
#define kAppSettingsFbPost2Key      @"post2"

#define kAppSettingsUnitKey         @"unit"
#define kAppSettingsDailyNotifKey   @"dailynotif"
#define kAppSettingsNotifKey        @"notifallcalories"

static AppSettings *_sharedSettings = nil;

@implementation AppSettings

+ (AppSettings *)initSettingsWithUserUid:(int)useruid
{
    _sharedSettings = [[AppSettings alloc] initWithUserUid:useruid];
    return _sharedSettings;
}

+ (AppSettings *)sharedSettings
{
    return _sharedSettings;
}

- (id)initWithUserUid:(int)useruid
{
    self = [super init];
    self.useruid = useruid;
    [self load];
    return self;
}

- (void)load
{
    NSDictionary *dicData = [[NSUserDefaults standardUserDefaults] objectForKey:[NSString stringWithFormat:@"appsettings_%d", self.useruid]];
    
    if (dicData == nil)
    {
        self.twitterUser = @"";
        self.twitterPwd = @"";
        self.isTweetWhenAllCalories = NO;
        self.isTweetPer500 = NO;
        self.fbUser = @"";
        self.fbPwd = @"";
        self.isPostWhenAllCalories = NO;
        self.isPostPer500 = NO;
        self.unit = MetricImperalUnitMetric;
        self.isDailyNotification = NO;
        self.isNotificationWhenAllCalories = NO;
    }
    else
    {
        NSObject *obj;
        // twitter user
        obj = [dicData objectForKey:kAppSettingsTwitterUserKey];
        if (obj == nil || ![obj isKindOfClass:[NSString class]])
            self.twitterUser = @"";
        else
            self.twitterUser = [NSString stringWithFormat:@"%@", (NSString *)obj];
        
        // twitter pwd
        obj = [dicData objectForKey:kAppSettingsTwitterPwdKey];
        if (obj == nil || ![obj isKindOfClass:[NSString class]])
            self.twitterPwd = @"";
        else
            self.twitterPwd = [NSString stringWithFormat:@"%@", (NSString *)obj];
        
        // tweet when all calories consumed
        obj = [dicData objectForKey:kAppSettingsTwitterTweet1Key];
        if (obj == nil || ![obj isKindOfClass:[NSNumber class]])
            self.isTweetWhenAllCalories = NO;
        else
            self.isTweetWhenAllCalories = [(NSNumber *)obj boolValue];
        
        // tweet per 500 calories burned
        obj = [dicData objectForKey:kAppSettingsTwitterTweet2Key];
        if (obj == nil || ![obj isKindOfClass:[NSNumber class]])
            self.isTweetPer500 = NO;
        else
            self.isTweetPer500 = [(NSString *)obj boolValue];
        
        // fb user
        obj = [dicData objectForKey:kAppSettingsFbUserKey];
        if (obj == nil || ![obj isKindOfClass:[NSString class]])
            self.fbUser = @"";
        else
            self.fbUser = [NSString stringWithFormat:@"%@", (NSString *)obj];
        
        obj = [dicData objectForKey:kAppSettingsFbPwdKey];
        if (obj == nil || ![obj isKindOfClass:[NSString class]])
            self.fbPwd = @"";
        else
            self.fbPwd = [NSString stringWithFormat:@"%@", (NSString *)obj];
        
        obj = [dicData objectForKey:kAppSettingsFbPost1Key];
        if (obj == nil || ![obj isKindOfClass:[NSNumber class]])
            self.isPostWhenAllCalories = NO;
        else
            self.isPostWhenAllCalories = [(NSNumber *)obj boolValue];
        
        obj = [dicData objectForKey:kAppSettingsFbPost2Key];
        if (obj == nil || ![obj isKindOfClass:[NSNumber class]])
            self.isPostPer500 = NO;
        else
            self.isPostPer500 = [(NSNumber *)obj boolValue];
        
        // unit
        obj = [dicData objectForKey:kAppSettingsUnitKey];
        if (obj == nil || ![obj isKindOfClass:[NSNumber class]])
            self.unit = MetricImperalUnitMetric;
        else
            self.unit = [(NSNumber *)obj intValue];
        
        // daily notif
        obj = [dicData objectForKey:kAppSettingsDailyNotifKey];
        if (obj == nil || ![obj isKindOfClass:[NSNumber class]])
            self.isDailyNotification = NO;
        else
            self.isDailyNotification = [(NSNumber *)obj boolValue];
        
        // notification when all calories burned
        obj = [dicData objectForKey:kAppSettingsNotifKey];
        if (obj == nil || ![obj isKindOfClass:[NSNumber class]])
            self.isNotificationWhenAllCalories = NO;
        else
            self.isNotificationWhenAllCalories = [(NSNumber *)obj boolValue];
    }
    
}

- (void)save
{

    NSMutableDictionary *dicData = [[NSMutableDictionary alloc] init];
    [dicData setObject:self.twitterUser forKey:kAppSettingsTwitterUserKey];
    [dicData setObject:self.twitterPwd forKey:kAppSettingsTwitterPwdKey];
    [dicData setObject:@(self.isTweetWhenAllCalories) forKey:kAppSettingsTwitterTweet1Key];
    [dicData setObject:@(self.isTweetPer500) forKey:kAppSettingsTwitterTweet2Key];
    
    [dicData setObject:self.fbUser forKey:kAppSettingsFbUserKey];
    [dicData setObject:self.fbPwd forKey:kAppSettingsFbPwdKey];
    [dicData setObject:@(self.isPostWhenAllCalories) forKey:kAppSettingsFbPost1Key];
    [dicData setObject:@(self.isPostPer500) forKey:kAppSettingsFbPost2Key];
    
    [dicData setObject:@(self.unit) forKey:kAppSettingsUnitKey];
    [dicData setObject:@(self.isDailyNotification) forKey:kAppSettingsDailyNotifKey];
    [dicData setObject:@(self.isNotificationWhenAllCalories) forKey:kAppSettingsNotifKey];
    [[NSUserDefaults standardUserDefaults] setObject:dicData forKey:[NSString stringWithFormat:@"appsettings_%d", self.useruid]];
    [[NSUserDefaults standardUserDefaults] synchronize];
}

@end
