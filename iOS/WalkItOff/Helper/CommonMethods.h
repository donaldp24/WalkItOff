//
//  CommonMethods.h
//  WalkItOff
//
//  Created by Donald Pae on 6/10/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface CommonMethods : NSObject

+ (NSComparisonResult)compareOnlyDate:(NSDate *)date1 date2:(NSDate *)date2;
+ (NSString *)date2str:(NSDate *)convertDate withFormat:(NSString *)formatString;
+ (NSDate *)str2date:(NSString *)dateString withFormat:(NSString *)formatString;

@end
