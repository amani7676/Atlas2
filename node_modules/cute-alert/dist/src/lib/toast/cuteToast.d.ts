import { ToastOptions } from '../../types/options';

export declare const closeToast: (toastWrapper: Element, toast: HTMLDivElement) => void;
export declare const cuteToast: ({ type, title, description, timer, vibrationPattern, soundSrc, imageSrc, imageSize }: ToastOptions) => Promise<"close">;
