@extends('layouts.site')

@section('content')
<section class="hero">
    <div class="wrap">
        <p class="kicker">Software Engineer</p>
        <h1>I build AI products at Meta.</h1>
        <p class="lede">Software engineer focused on AI product engineering — building products end to end, from the interface to the infrastructure.</p>
        <div class="hero-links">
            <a class="text-link" href="https://github.com/mattquest" target="_blank" rel="noopener">GitHub ↗</a>
            <a class="text-link" href="https://stackoverflow.com/users/2227763/wheelmaker" target="_blank" rel="noopener">Stack Overflow ↗</a>
            <button class="btn-resume" id="resumeBtn" type="button">Download résumé ↓</button>
        </div>
        @if(session('resume_error'))
            <p class="form-error" role="alert">{{ session('resume_error') }}</p>
        @endif
        <form id="resumeForm" method="post" action="/download-resume" class="visually-hidden" aria-hidden="true" tabindex="-1">
            @csrf
            <input type="hidden" name="token" id="resumeToken">
        </form>
    </div>
</section>

<section class="section" id="now">
    <div class="wrap">
        <h2 class="section-head"><span class="section-num">01</span> Now</h2>
        <div class="now-list">
            <article class="now-item">
                <div>
                    <h3>Meta — AI product engineering</h3>
                    <p>Building the products, and the systems behind them.</p>
                </div>
                <span class="tag">Current role</span>
            </article>
            <article class="now-item">
                <div>
                    <h3>Miloh</h3>
                    <p>A fitness app I'm building — coaching that adapts to the athlete, not the other way around.</p>
                </div>
                <span class="tag">In progress</span>
            </article>
            <article class="now-item">
                <div>
                    <h3>ROENWOOD</h3>
                    <p>General contracting — commercial and residential construction.</p>
                </div>
                <span class="tag">Est. 2026</span>
            </article>
        </div>
    </div>
</section>

<section class="section" id="work">
    <div class="wrap">
        <h2 class="section-head"><span class="section-num">02</span> Work</h2>
        <div class="work-list">
            <article class="work-item">
                <h3>Miloh</h3>
                <p>Adaptive fitness coaching. Currently in development.</p>
                <span class="tag">In progress</span>
            </article>
            <article class="work-item">
                <h3><a href="https://save.ws" target="_blank" rel="noopener">save.ws ↗</a></h3>
                <p>Price comparisons for health tests and everyday buys — the short version of hours of research.</p>
            </article>
            <article class="work-item">
                <h3><a href="https://www.roenwood.com" target="_blank" rel="noopener">ROENWOOD ↗</a></h3>
                <p>LeCrockemacher, LLC — general contracting.</p>
            </article>
            <article class="work-item">
                <h3><a href="https://github.com/mattquest" target="_blank" rel="noopener">GitHub ↗</a></h3>
                <p>Open-source work and side projects, under mattquest.</p>
            </article>
        </div>
        <p class="earlier">Earlier: Laravue SPA, Liftmas, Coveted Clothing — small businesses and tools from a previous chapter.</p>
    </div>
</section>

<section class="section" id="about">
    <div class="wrap narrow">
        <h2 class="section-head"><span class="section-num">03</span> About</h2>
        <p>Most of my career has been building web products end to end — the interface, the API, the infrastructure, all of it. These days that work is AI product engineering at Meta.</p>
        <p>On the side I build products and run ventures with the same standard: well made, plainly stated. This page included.</p>
    </div>
</section>

<section class="section" id="contact">
    <div class="wrap narrow">
        <h2 class="section-head"><span class="section-num">04</span> Contact</h2>
        <p class="contact-intro">Want to talk about a project, a role, or a collaboration? Send a note — it goes straight to my inbox.</p>
        <form id="contactForm" method="post" action="/contact" novalidate>
            @csrf
            <div class="field">
                <label for="cf-name">Name</label>
                <input id="cf-name" name="name" type="text" autocomplete="name" required maxlength="100">
            </div>
            <div class="field">
                <label for="cf-email">Email</label>
                <input id="cf-email" name="email" type="email" autocomplete="email" required maxlength="150">
            </div>
            <div class="field">
                <label for="cf-company">Company <span class="optional">(optional)</span></label>
                <input id="cf-company" name="company" type="text" autocomplete="organization" maxlength="100">
            </div>
            <div class="field">
                <label for="cf-message">Message</label>
                <textarea id="cf-message" name="message" rows="5" required maxlength="5000"></textarea>
            </div>
            <div class="field honeypot" aria-hidden="true">
                <label for="cf-website">Website</label>
                <input id="cf-website" name="website" type="text" tabindex="-1" autocomplete="off">
            </div>
            <input type="hidden" name="token" id="contactToken">
            <button class="btn-primary" type="submit" id="contactSubmit">Send message</button>
            <p class="form-status" id="contactStatus" role="status" aria-live="polite"></p>
        </form>
    </div>
</section>
@endsection
