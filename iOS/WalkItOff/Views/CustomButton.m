//
//  CustomButton.m
//  WalkItOff
//
//  Created by Donald Pae on 6/16/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "CustomButton.h"

@implementation UIButton(Custom)

- (CGSize) intrinsicContentSize {
    
    CGSize s = [super intrinsicContentSize];
    
    return CGSizeMake(s.width + self.titleEdgeInsets.left + self.titleEdgeInsets.right,
                      s.height + self.titleEdgeInsets.top + self.titleEdgeInsets.bottom);
    
}

@end
