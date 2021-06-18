export default {
  classes: {
    wizardContainer: 'wizard w-full',
    wizardStepsContainer: 'relative flex items-center justify-center',
    navigationContainer:
      'flex flex-col items-center justify-between h-32 step-indicator',
    progressesContainer:
      'box-border relative flex justify-around mt-16 border-4 border-gray-200 border-solid rounded-md indicator-line',
    progressesSubContainer: 'absolute flex justify-between center',
    progress: 'rounded-full steps cursor-pointer',
    currentStep: 'bg-white border-primary-500',
    previousStep:
      'bg-primary-500 border-primary-500 flex justify-center items-center',
    nextStep: 'border-gray-200 bg-white',
    icon:
      'flex items-center justify-center w-full h-full text-sm font-black text-center text-white',
    stepContainer:
      'w-full mb-8 bg-white border border-gray-200 border-solid rounded p-8 lg:w-9/12 md:w-full relative',
    stepHeadingContainer: 'heading-section',
    stepTitle: 'text-2xl not-italic font-semibold leading-7 text-black',
    stepDescription:
      'w-full mt-2.5 mb-8 text-sm not-italic text-gray-600 lg:w-7/12 md:w-7/12 sm:w-7/12',
  },
  variants: {},
}
