//
//  CommonMethods.m
//  WalkItOff
//
//  Created by Donald Pae on 6/10/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "CommonMethods.h"

@implementation CommonMethods

+ (NSComparisonResult)compareOnlyDate:(NSDate *)date1 date2:(NSDate *)date2
{
    NSUInteger dateFlags = NSYearCalendarUnit|NSMonthCalendarUnit|NSDayCalendarUnit;
    NSCalendar *gregorianCalendar = [[NSCalendar alloc] initWithCalendarIdentifier:NSGregorianCalendar];
    NSDateComponents *selfComponents = [gregorianCalendar components:dateFlags fromDate:date1];
    NSDate *selfDateOnly = [gregorianCalendar dateFromComponents:selfComponents];
    
    NSDateComponents *otherCompents = [gregorianCalendar components:dateFlags fromDate:date2];
    NSDate *otherDateOnly = [gregorianCalendar dateFromComponents:otherCompents];
    return [selfDateOnly compare:otherDateOnly];
}

+ (NSString *)date2str:(NSDate *)convertDate withFormat:(NSString *)formatString
{
    NSDateFormatter *dateFormatter = [[NSDateFormatter alloc] init];
    [dateFormatter setDateFormat:formatString];
    
    return [dateFormatter stringFromDate:convertDate];
}

+ (NSDate *)str2date:(NSString *)dateString withFormat:(NSString *)formatString
{
    NSDateFormatter *dateFormatter = [[NSDateFormatter alloc] init];
    [dateFormatter setDateFormat:formatString];
    return [dateFormatter dateFromString:dateString];
}

@end
