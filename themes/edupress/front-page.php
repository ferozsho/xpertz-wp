<?php
/**
 * Custom Premium Homepage Template for EduPress
 */

get_header();

// Fetch live counts dynamically from the database
$count_users = count_users();
$student_count = $count_users['avail_roles']['subscriber'] ?? 48;
$teacher_count = $count_users['avail_roles']['lp_teacher'] ?? 5;
$course_count  = wp_count_posts( 'lp_course' )->publish ?? 10;
?>

<!-- Hero Section -->
<section class="premium-hero">
    <div class="wrapper">
        <h1>Unlock Your Future with <span>Advanced Learning</span></h1>
        <p class="tagline">Explore immersive courses, learn from top industry experts, and advance your career with our state-of-the-art educational catalog.</p>
        <div class="cta-buttons">
            <a href="<?php echo esc_url( home_url( '/courses/' ) ); ?>" class="cta-primary">Browse All Courses</a>
            <a href="#benefits" class="cta-secondary">Why Choose Us</a>
        </div>
    </div>
</section>

<!-- Stats Dashboard -->
<section class="premium-stats">
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-number"><?php echo esc_html( $course_count ); ?>+</div>
            <div class="stat-label">Premium Courses</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?php echo esc_html( $student_count ); ?>+</div>
            <div class="stat-label">Active Students</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?php echo esc_html( $teacher_count ); ?>+</div>
            <div class="stat-label">Expert Teachers</div>
        </div>
    </div>
</section>

<!-- Featured Courses Grid -->
<section class="home-section">
    <div class="courses-container">
        <div class="section-title">
            <h2>Explore Featured Courses</h2>
            <p>Start learning today with our high-quality premium training courses, structured for career progression.</p>
        </div>
        
        <div class="courses-grid">
            <?php
            $args = array(
                'post_type'      => 'lp_course',
                'posts_per_page' => 6,
                'post_status'    => 'publish'
            );
            $query = new WP_Query( $args );
            
            if ( $query->have_posts() ) :
                while ( $query->have_posts() ) : $query->the_post();
                    $course_id = get_the_ID();
                    $instructor_name = get_the_author();
                    
                    // Price/WooCommerce Integration
                    $product_id = get_post_meta( $course_id, '_related_woocommerce_product_id', true );
                    $price_display = 'Free';
                    if ( $product_id ) {
                        $product = wc_get_product( $product_id );
                        if ( $product ) {
                            $price_display = wc_price( $product->get_price() );
                        }
                    }
                    ?>
                    <div class="course-card">
                        <div class="course-card-image">
                            <span class="badge">Course</span>
                            <?php if ( has_post_thumbnail() ) : ?>
                                <?php the_post_thumbnail( 'medium' ); ?>
                            <?php else : ?>
                                <span><?php the_title(); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="course-card-content">
                            <h3 class="course-card-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>
                            <div class="course-card-meta">
                                <span class="course-card-instructor">By <?php echo esc_html( $instructor_name ); ?></span>
                                <span class="course-card-price"><?php echo $price_display; ?></span>
                            </div>
                        </div>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
            else :
                ?>
                <p style="grid-column: 1/-1; text-align: center; color: var(--text-muted);">No courses found. Please add courses in the WordPress admin.</p>
                <?php
            endif;
            ?>
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section id="benefits" class="home-section" style="background-color: #ffffff; border-top: 1px solid rgba(0,0,0,0.02);">
    <div class="section-title">
        <h2>Why Choose Our Platform?</h2>
        <p>Built from the ground up to offer the most immersive, flexible, and comprehensive learning experiences.</p>
    </div>
    
    <div class="benefits-grid">
        <div class="benefit-card">
            <div class="benefit-icon">🎓</div>
            <h3>Expert Instructors</h3>
            <p>Our educators are active industry professionals with years of hands-on technical experience.</p>
        </div>
        <div class="benefit-card">
            <div class="benefit-icon">⚡</div>
            <h3>Flexible Learning</h3>
            <p>Study at your own pace from anywhere in the world on desktop, tablet, or mobile devices.</p>
        </div>
        <div class="benefit-card">
            <div class="benefit-icon">🏆</div>
            <h3>WooCommerce Payments</h3>
            <p>Secure checkout and immediate course enrollment powered by the WooCommerce billing platform.</p>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="home-section" style="background-color: #f7f9fc;">
    <div class="section-title">
        <h2>What Our Students Say</h2>
        <p>Real reviews from learners who successfully accelerated their technical careers.</p>
    </div>
    
    <div class="benefits-grid">
        <div class="benefit-card" style="box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
            <p style="font-style: italic; color: var(--text-muted); margin-bottom: 20px; line-height: 1.6;">"The integration is incredibly seamless. I bought the course via WooCommerce checkout and was immediately enrolled with full curriculum access. Highly recommended!"</p>
            <h4 style="margin: 0; font-size: 16px;">Alex Rivera</h4>
            <span style="font-size: 13px; color: var(--text-muted);">Student</span>
        </div>
        <div class="benefit-card" style="box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
            <p style="font-style: italic; color: var(--text-muted); margin-bottom: 20px; line-height: 1.6;">"The course structure is so easy to follow, and the quiz attempts and lesson completions track very nicely in my user dashboard page."</p>
            <h4 style="margin: 0; font-size: 16px;">Jordan Miller</h4>
            <span style="font-size: 13px; color: var(--text-muted);">Student</span>
        </div>
        <div class="benefit-card" style="box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
            <p style="font-style: italic; color: var(--text-muted); margin-bottom: 20px; line-height: 1.6;">"Perfect LMS platform. The WooCommerce checkout and payment system are fast, and the dashboard provides excellent visibility into student progress."</p>
            <h4 style="margin: 0; font-size: 16px;">Casey Chen</h4>
            <span style="font-size: 13px; color: var(--text-muted);">Student</span>
        </div>
    </div>
</section>

<?php
get_footer();
