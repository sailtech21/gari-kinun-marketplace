// Firebase Configuration and Setup
import { initializeApp } from 'firebase/app';
import { 
  getAuth, 
  GoogleAuthProvider,
  RecaptchaVerifier,
  signInWithPopup,
  signInWithPhoneNumber,
  signOut as firebaseSignOut
} from 'firebase/auth';

// Firebase configuration - Replace with your actual config from Firebase Console
// To keep credentials secure, use environment variables
const firebaseConfig = {
  apiKey: import.meta.env.VITE_FIREBASE_API_KEY || "AIzaSyBLYSfaIuE0t2mPHaqPr7hNgycIDFP2NDQ",
  authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN || "garikinun-bb120.firebaseapp.com",
  projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID || "garikinun-bb120",
  storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET || "garikinun-bb120.firebasestorage.app",
  messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID || "949156489690",
  appId: import.meta.env.VITE_FIREBASE_APP_ID || "1:949156489690:web:2ca69388b367adb87078e3"
};

// Initialize Firebase
const app = initializeApp(firebaseConfig);

// Initialize Firebase Authentication and get a reference to the service
export const auth = getAuth(app);

// Google Auth Provider
export const googleProvider = new GoogleAuthProvider();
googleProvider.setCustomParameters({
  prompt: 'select_account' // Always show account selection
});

// Setup reCAPTCHA verifier for phone authentication
let recaptchaVerifier = null;

export const setupRecaptcha = (containerId = 'recaptcha-container') => {
  // Clear existing verifier if any
  if (recaptchaVerifier) {
    recaptchaVerifier.clear();
  }

  recaptchaVerifier = new RecaptchaVerifier(auth, containerId, {
    size: 'invisible',
    callback: (response) => {
      // reCAPTCHA solved - allow phone auth to proceed
      console.log('reCAPTCHA verified');
    },
    'expired-callback': () => {
      console.log('reCAPTCHA expired, please try again');
      // You might want to show a message to the user
    }
  });

  return recaptchaVerifier;
};

// Get the current reCAPTCHA verifier instance
export const getRecaptchaVerifier = () => {
  return recaptchaVerifier;
};

// Clear reCAPTCHA verifier
export const clearRecaptcha = () => {
  if (recaptchaVerifier) {
    recaptchaVerifier.clear();
    recaptchaVerifier = null;
  }
};

// Sign in with Google
export const signInWithGoogle = async () => {
  try {
    const result = await signInWithPopup(auth, googleProvider);
    return result.user;
  } catch (error) {
    console.error('Google sign-in error:', error);
    throw error;
  }
};

// Send OTP to phone number
export const sendPhoneOTP = async (phoneNumber) => {
  try {
    // Ensure phone number has country code
    const formattedPhone = phoneNumber.startsWith('+') ? phoneNumber : `+88${phoneNumber}`;
    
    // Setup reCAPTCHA if not already done
    if (!recaptchaVerifier) {
      setupRecaptcha();
    }

    // Send OTP
    const confirmationResult = await signInWithPhoneNumber(auth, formattedPhone, recaptchaVerifier);
    return confirmationResult;
  } catch (error) {
    console.error('Phone OTP error:', error);
    
    // Clear reCAPTCHA on error so it can be retried
    clearRecaptcha();
    
    // Handle specific error codes
    if (error.code === 'auth/invalid-phone-number') {
      throw new Error('Invalid phone number format. Use +880XXXXXXXXXX');
    } else if (error.code === 'auth/too-many-requests') {
      throw new Error('Too many requests. Please try again later.');
    } else if (error.code === 'auth/captcha-check-failed') {
      throw new Error('reCAPTCHA verification failed. Please try again.');
    }
    
    throw error;
  }
};

// Verify OTP code
export const verifyPhoneOTP = async (confirmationResult, otpCode) => {
  try {
    const result = await confirmationResult.confirm(otpCode);
    return result.user;
  } catch (error) {
    console.error('OTP verification error:', error);
    
    if (error.code === 'auth/invalid-verification-code') {
      throw new Error('Invalid OTP code. Please check and try again.');
    } else if (error.code === 'auth/code-expired') {
      throw new Error('OTP code expired. Please request a new code.');
    }
    
    throw error;
  }
};

// Sign out
export const signOut = async () => {
  try {
    await firebaseSignOut(auth);
    clearRecaptcha();
  } catch (error) {
    console.error('Sign out error:', error);
    throw error;
  }
};

// Get Firebase ID token (for authenticating with your backend)
export const getFirebaseToken = async () => {
  const user = auth.currentUser;
  if (user) {
    return await user.getIdToken();
  }
  return null;
};

// Listen to auth state changes
export const onAuthStateChanged = (callback) => {
  return auth.onAuthStateChanged(callback);
};

export { signInWithPopup, signInWithPhoneNumber };
export default app;
