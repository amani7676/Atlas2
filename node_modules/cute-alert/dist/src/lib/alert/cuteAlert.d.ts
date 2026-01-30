import { AlertOptions } from '../../types/options';

export declare const cuteAlert: ({ type, title, description, timer, primaryButtonText, secondaryButtonText, vibrationPattern, soundSrc, closeStyle, imageSrc, imageSize }: AlertOptions) => Promise<"primaryButtonClicked" | "secondaryButtonClicked" | "close">;
