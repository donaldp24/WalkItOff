//
//  AppSettings.h
//  WalkItOff
//
//  Created by Donald Pae on 7/13/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import <Foundation/Foundation.h>

typedef enum {
    MetricImperalUnitMetric = 0,
    MetricImperalUnitImperal
}MetricImperalUnit;

@interface AppSettings : NSObject

@property (nonatomic) int useruid;

// twitter
@property (nonatomic, strong) NSString *twitterUser;
@property (nonatomic, strong) NSString *twitterPwd;
@property (nonatomic) BOOL isTweetWhenAllCalories;
@property (nonatomic) BOOL isTweetPer500;

//facebook
@property (nonatomic, strong) NSString *fbUser;
@property (nonatomic, strong) NSString *fbPwd;
@property (nonatomic) BOOL isPostWhenAllCalories;
@property (nonatomic) BOOL isPostPer500;

//app
@property (nonatomic) MetricImperalUnit unit;
@property (nonatomic) BOOL isDailyNotification;
@property (nonatomic) BOOL isNotificationWhenAllCalories;

+ (AppSettings *)initSettingsWithUserUid:(int)useruid;
+ (AppSettings *)sharedSettings;
- (void)save;

@end
