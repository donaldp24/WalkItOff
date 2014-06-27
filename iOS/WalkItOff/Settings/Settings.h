//
//  Settings.h
//  WalkItOff
//
//  Created by Donald Pae on 6/8/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "AppProfile.h"

@interface Settings : NSObject

- (void)read:(AppProfile *)profile;
- (void)write:(AppProfile *)profile;

@end
