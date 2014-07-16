//
//  Formulas+walkitoff.m
//  WalkItOff
//
//  Created by Donald Pae on 7/3/14.
//  Copyright (c) 2014 daniel. All rights reserved.
//

#import "Formulas+walkitoff.h"

@implementation Formulas

+ (CGFloat)percentageOfCaloriesBurned:(CGFloat)caloriesRemaining totalCurrentMealsCalories:(CGFloat)totalCurrentMealsCalories
{
    if (totalCurrentMealsCalories == 0)
        return 0;
    return caloriesRemaining / totalCurrentMealsCalories * 100;
}

+ (CGFloat)caloriesRemaining:(CGFloat)userCaloriesBurned totalCalories:(CGFloat)totalCurrentMealsCalories
{
    return totalCurrentMealsCalories - userCaloriesBurned;
}

+ (CGFloat)distanceRemaining:(CGFloat)distanceWalked totalDistanceToWalk:(CGFloat)totalDistanceToWalk
{
    return totalDistanceToWalk - distanceWalked;
}

+ (CGFloat)stepsRemaining:(CGFloat)stepsWalked totalStepsToBeWalkedToBurnFood:(CGFloat)totalStepsToBeWalkedToBurnFood
{
    return totalStepsToBeWalkedToBurnFood - stepsWalked;
}

+ (CGFloat)estimatedTimeToWalkAtCasualPace:(CGFloat)distanceRemainingInMiles
{
    return distanceRemainingInMiles / 2 /*miles*/ * 3600/*per hour*/;
}

+ (CGFloat)estimatedTimeToWalkAtBriskPace:(CGFloat)distanceRemainingInMiles
{
    return distanceRemainingInMiles / 3.5 /*miles*/ * 3600/*per hour*/;
}

+ (CGFloat)userCaloriesBurnedPerMile:(CGFloat)userWeightInLbs
{
    return userWeightInLbs * 0.57;
}

+ (CGFloat)userStrideLengthInMiles:(CGFloat)userHeightInCm
{
    return userHeightInCm * 0.4 * 0.0000062137;
}

+ (CGFloat)userCaloriesBurnedPerStep:(CGFloat)userCaloriesburnedPerMile strideLengthInMiles:(CGFloat)strideLengthInMiles
{
    return userCaloriesburnedPerMile * strideLengthInMiles;
}


+ (CGFloat)caloriesInFood:(CGFloat)caloriesInFood
{
    return caloriesInFood;
}

+ (CGFloat)stepsToBeWalkedToBurnFood:(CGFloat)caloriesInFood userCaloriesBurnedPerStep:(CGFloat)userCaloriesBurnedPerStep
{
    if (userCaloriesBurnedPerStep == 0)
        return 0;
    return caloriesInFood / userCaloriesBurnedPerStep;
}

+ (CGFloat)distanceToWalkToBurnFood:(CGFloat)userStrideLength stepsPerFood:(CGFloat)stepsPerFood
{
    if (stepsPerFood == 0)
        return 0;
    return userStrideLength / stepsPerFood;
}

+ (CGFloat)distanceWalked:(CGFloat)userStrideLength numberOfSteps:(NSInteger)numberOfSteps
{
    return userStrideLength * numberOfSteps;
}

+ (NSInteger)stepsWalked:(NSInteger)numberOfSteps
{
    return numberOfSteps;
}

+ (CGFloat)weightInLbsWithKg:(CGFloat)weightInKg
{
    return (weightInKg / 0.45359237);
}

@end
