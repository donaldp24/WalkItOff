//
//  AutoMessageBox.h
//  Showhand
//
//  Created by Lion User on 01/02/2013.
//  Copyright (c) 2013 AppDevCenter. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface AutoMessageBox : UIViewController{
    IBOutlet UILabel        *lblMsg;
    IBOutlet UIImageView    *imgSuccess;
    IBOutlet UIImageView    *imgFailure;
    
    NSString            *strMsg;
    BOOL                bSuccess;
}

@property(nonatomic, readwrite) BOOL    bSuccess;
@property(nonatomic, retain) NSString   *strMsg;

+ (void)AutoMsgInView:(UIViewController *)parentViewController withText:(NSString *)text withSuccess:(BOOL)success;

@end
