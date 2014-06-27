//
//  Food.h
//  WalkItOff
//
//  Created by Donald Pae on 6/13/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface Food : NSObject

@property (nonatomic, assign) int uid;
@property (nonatomic, strong) NSString *name;
@property (nonatomic, assign) CGFloat calories;

@end
