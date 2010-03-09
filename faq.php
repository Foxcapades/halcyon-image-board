<?php
/**
 *
 *	Halcyon Image Board
 *  Copyright (C) 2010  Steven Utiger
 *
 *    This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, either version 3 of the License, or any later version.
 *
 *    This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 *    You should have received a copy of the GNU General Public License along
 * with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

session_start();



if(file_exists('n/cnf.php')) {
	require_once 'n/cnf.php';
} else {
	die('dude, wtf');
}

$body = '<div class="longnote thread"><h2>Privacy Policy</h2>

<p>Your privacy is critically important to us. At HalcyonBBS we have a few fundamental principles:
<br />
<ul>
	<li>We don\'t ask you for personal information unless we truly need it. (We can\'t stand services that ask you for things like your gender or income level for no apparent reason.)</li>
	<li>We don\'t share your personal information with anyone except to comply with the law, develop our products, or protect our rights.</li>
	<li>We don\'t store personal information on our servers unless required for the on-going operation of one of our services.</li>
	<li>We aim to make it as simple as possible for you to control what\'s visible to the public, seen by search engines, kept private, and permanently deleted.</li>
</ul></p>
<br />
<p>Below is our privacy policy which incorporates these goals:</p>
<br />
<p>Creative Commons License (This Privacy Policy is used and edited under the Creative Commons Sharealike license.  The original may be found at automattic.com.)</p>
<br />
<p>Halcyon Bulletin Board Systems. (&quot;HalcyonBBS&quot;) operates several websites including, but not limited to Halcyonbbs.com and Ovar9k.com. It is HalcyonBBS\'s policy to respect your privacy regarding any information we may collect while operating our websites.</p>
<br />
<h3>Website Visitors</h3>
<br />
<p>Like most website operators, HalcyonBBS collects non-personally-identifying information of the sort that web browsers and servers typically make available, such as the browser type, language preference, referring site, and the date and time of each visitor request. HalcyonBBS\'s purpose in collecting non-personally identifying information is to better understand how HalcyonBBS\'s visitors use its website. From time to time, HalcyonBBS may release non-personally-identifying information in the aggregate, e.g., by publishing a report on trends in the usage of its website.</p>
<br />
<p>HalcyonBBS also collects potentially personally-identifying information like Internet Protocol (IP) addresses. HalcyonBBS does not use such information to identify its visitors, however, and does not disclose such information, other than under the same circumstances that it uses and discloses personally-identifying information, as described below.</p>
<br />
<h3>Gathering of Personally-Identifying Information</h3>
<br />
<p>Certain visitors to HalcyonBBS\'s websites choose to interact with HalcyonBBS in ways that require HalcyonBBS to gather personally-identifying information. The amount and type of information that HalcyonBBS gathers depends on the nature of the interaction. For example, we ask visitors who sign up for an account at halcyonbbs.com to provide a username and email address. HalcyonBBS collects such information only insofar as is necessary or appropriate to fulfill the purpose of the visitor\'s interaction with HalcyonBBS. HalcyonBBS does not disclose personally-identifying information other than as described below. And visitors can always refuse to supply personally-identifying information, with the caveat that it may prevent them from engaging in certain website-related activities.</p>
<br />
<h3>Aggregated Statistics</h3>
<br />
<p>HalcyonBBS may collect statistics about the behavior of visitors to its websites. HalcyonBBS may display this information publicly or provide it to others. However, HalcyonBBS does not disclose personally-identifying information other than as described below.</p>
<br />
<h3>Protection of Certain Personally-Identifying Information</h3>
<br />
<p>HalcyonBBS discloses potentially personally-identifying and personally-identifying information only to those of its employees, contractors and affiliated organizations that (i) need to know that information in order to process it on HalcyonBBS\'s behalf or to provide services available at HalcyonBBS\'s websites, and (ii) that have agreed not to disclose it to others. Some of those employees, contractors and affiliated organizations may be located outside of your home country; by using HalcyonBBS\'s websites, you consent to the transfer of such information to them. HalcyonBBS will not rent or sell potentially personally-identifying and personally-identifying information to anyone. Other than to its employees, contractors and affiliated organizations, as described above, HalcyonBBS discloses potentially personally-identifying and personally-identifying information only when required to do so by law, or when HalcyonBBS believes in good faith that disclosure is reasonably necessary to protect the property or rights of HalcyonBBS, third parties or the public at large. If you are a registered user of an HalcyonBBS website and have supplied your email address, HalcyonBBS may occasionally send you an email to tell you about new features, solicit your feedback, or just keep you up to date with what\'s going on with HalcyonBBS and our products. We primarily use our own bulletin boards to communicate this type of information, so we expect to keep this type of email to a minimum. If you send us a request (for example via a support email or via one of our feedback mechanisms), we reserve the right to publish it in order to help us clarify or respond to your request or to help us support other users. HalcyonBBS takes all measures reasonably necessary to protect against the unauthorized access, use, alteration or destruction of potentially personally-identifying and personally-identifying information.</p>
<br />
<h3>Cookies</h3>
<br />
<p>A cookie is a string of information that a website stores on a visitor\'s computer, and that the visitor\'s browser provides to the website each time the visitor returns. HalcyonBBS uses cookies to help HalcyonBBS identify and track visitors, their usage of HalcyonBBS website, and their website access preferences. HalcyonBBS visitors who do not wish to have cookies placed on their computers should set their browsers to refuse cookies before using HalcyonBBS\'s websites, with the drawback that certain features of HalcyonBBS\'s websites may not function properly without the aid of cookies.</p>
<br />
<h3>Ads</h3>
<br />
<p>Ads appearing on any of our websites may be delivered to users by advertising partners, who may set cookies. These cookies allow the ad server to recognize your computer each time they send you an online advertisement to compile information about you or others who use your computer. This information allows ad networks to, among other things, deliver targeted advertisements that they believe will be of most interest to you. This Privacy Policy covers the use of cookies by HalcyonBBS and does not cover the use of cookies by any advertisers.</p>
<br />
<h3>Privacy Policy Changes</h3>
<br />
<p>Although most changes are likely to be minor, HalcyonBBS may change its Privacy Poicy from time to time, and in HalcyonBBS\'s sole discretion. HalcyonBBS encourages visitors to frequently check this page for any changes to its Privacy Policy. Your continued use of this site after any change in this Privacy Policy will constitute your acceptance of such change.</p></div>';
$navbar = navbuild($SQL);
$P->set('navbar',$navbar);
$P->set('body',$body);
$P->load('base.php');
$P->render();
