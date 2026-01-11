<?php
$pageTitle = 'Contact Us';
$success = false;

require_once 'includes/Mailer.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? 'Website Inquiry';
    $message = $_POST['message'] ?? '';

    $mailer = new Mailer();
    $adminEmail = SMTP_USER;
    
    $body = "
        <p><strong>Name:</strong> {$name}</p>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Subject:</strong> {$subject}</p>
        <p><strong>Message:</strong></p>
        <div style='background: #f3f4f6; padding: 15px; border-radius: 8px;'>{$message}</div>
    ";

    if ($mailer->send($adminEmail, "Contact Form: " . $subject, $body)) {
        $success = true;
    } else {
        $errors[] = "Failed to send message. Please try again later.";
    }
}

ob_start();
?>

<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-3xl font-extrabold text-gray-900 sm:text-4xl font-heading">Get in Touch</h1>
            <p class="mt-4 text-lg text-gray-500">Have questions about buying or selling? We're here to help.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Info -->
            <div class="space-y-8">
                <!-- Info Cards -->
                <div class="bg-white rounded-2xl shadow-soft p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 font-heading">Contact Information</h3>
                    
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold text-gray-900">Office</h4>
                                <p class="mt-1 text-gray-600">
                                    Mvan, Yaoundé<br>
                                    Cameroon
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start">
                             <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold text-gray-900">Email</h4>
                                <p class="mt-1 text-gray-600">
                                    mishaelsema@gmail.com<br>
                                    mishusema237@gmail.com
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start">
                             <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center text-primary">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-lg font-semibold text-gray-900">Phone</h4>
                                <p class="mt-1 text-gray-600">
                                    +237 6XX XXX XXX<br>
                                    Mon-Fri from 8am to 5pm
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Abstract Map Placeholder -->
                <div class="bg-gray-200 rounded-2xl h-64 w-full flex items-center justify-center overflow-hidden relative">
                    <img src="https://source.unsplash.com/random/800x600?map" alt="Map Location" class="absolute inset-0 w-full h-full object-cover grayscale opacity-50">
                    <div class="relative z-10 bg-white/80 backdrop-blur px-6 py-3 rounded-xl shadow-sm">
                        <span class="font-bold text-gray-900 flex items-center gap-2">
                             <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                             Find us in Yaoundé
                        </span>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-white rounded-2xl shadow-soft p-8">
                <?php if ($success): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-8 rounded-xl text-center h-full flex flex-col items-center justify-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4 text-green-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Message Sent!</h3>
                    <p>Thank you for contacting us. We will get back to you shortly.</p>
                    <a href="index.php?page=contact" class="mt-6 text-primary font-semibold hover:underline">Send another message</a>
                </div>
                <?php else: ?>
                <h3 class="text-xl font-bold text-gray-900 mb-6 font-heading">Send us a Message</h3>
                <form action="" method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Your Name</label>
                            <input type="text" name="name" required class="input-modern" placeholder="John Doe">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input type="email" name="email" required class="input-modern" placeholder="john@example.com">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                         <select name="subject" class="input-modern">
                            <option>General Inquiry</option>
                            <option>Vendor Support</option>
                            <option>Report an Issue</option>
                            <option>Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                        <textarea name="message" rows="5" required class="input-modern" placeholder="How can we help you?"></textarea>
                    </div>

                    <button type="submit" class="btn-primary w-full py-3.5 shadow-lg shadow-primary/20">
                        Send Message
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'includes/layout.php';
?>
